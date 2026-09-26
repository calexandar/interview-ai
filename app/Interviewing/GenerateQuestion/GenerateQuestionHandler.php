<?php

namespace App\Interviewing\GenerateQuestion;

use App\AI\Contracts\InterviewAI;
use App\AI\Data\GeneratedQuestion;
use App\AI\Data\InterviewContext;
use App\AI\Exceptions\AIUnavailableException;
use App\Interviewing\DetermineNextQuestion\DetermineNextQuestion;
use App\Interviewing\DetermineNextQuestion\DetermineNextQuestionHandler;
use App\Interviewing\DetermineNextQuestion\NextQuestionPlan;
use App\Models\Answer;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Skill;
use App\Shared\Enums\QuestionDifficulty;
use App\Shared\Enums\QuestionSource;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Asks the AI for the next question, then decides what is actually true.
 *
 * Order matters here. The provider call happens before any transaction opens,
 * so a slow or failing provider never holds a database lock. The plan comes
 * from deterministic PHP, and the returned skill and difficulty are re-checked
 * against it: the AI can write a good question, but it cannot choose which
 * skill is assessed or jump the difficulty.
 */
class GenerateQuestionHandler
{
    public function __construct(
        private InterviewAI $ai,
        private DetermineNextQuestionHandler $planner,
    ) {}

    /**
     * @throws AIUnavailableException
     */
    public function handle(GenerateQuestion $command): InterviewQuestion
    {
        $interview = Interview::where('id', $command->interviewId)
            ->where('organization_id', $command->organizationId)
            ->firstOrFail();

        if (! $interview->isActive()) {
            abort(422, 'This interview is not active.');
        }

        if ($interview->hasReachedQuestionLimit()) {
            abort(422, 'This interview has already asked every question.');
        }

        $plan = $command->plan ?? $this->planner->handle(new DetermineNextQuestion(
            interviewId: $command->interviewId,
            organizationId: $command->organizationId,
        ));

        $context = $this->buildContext($interview, $plan);
        $generated = $this->ai->generateQuestion($context);

        $skill = $this->resolveSkill($interview, $plan, $generated);
        $difficulty = $this->resolveDifficulty($plan, $generated);
        $invocation = $this->ai->lastInvocation();

        return $this->persist($interview->id, $plan, $generated, $skill, $difficulty, $invocation);
    }

    /**
     * Builds a bounded context: only the questions and answers the AI actually
     * needs to avoid repetition, truncated so a long interview cannot grow the
     * prompt without limit.
     */
    private function buildContext(Interview $interview, NextQuestionPlan $plan): InterviewContext
    {
        $position = $interview->position;
        $limit = (int) config('interview.context.max_previous_questions', 5);

        $recent = $interview->interviewQuestions()
            ->with('answer')
            ->whereIn('status', [
                QuestionStatus::Asking,
                QuestionStatus::Answering,
                QuestionStatus::Processing,
                QuestionStatus::Answered,
            ])
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        $maxQuestionChars = (int) config('interview.context.max_history_question_chars', 1000);
        $maxAnswerChars = (int) config('interview.context.max_history_answer_chars', 1500);

        return new InterviewContext(
            jobTitle: $position->title,
            jobDescription: Str::limit((string) $position->description, (int) config('interview.context.max_job_description_chars', 2000), ''),
            requiredSkills: $position->skills()->pluck('name')->all(),
            candidateName: $interview->candidate->name,
            section: $plan->skillName,
            previousQuestions: $recent
                ->map(fn (InterviewQuestion $question): string => Str::limit($question->question_text, $maxQuestionChars, ''))
                ->all(),
            previousAnswers: $recent
                ->map(fn (InterviewQuestion $question): string => Str::limit($this->answerContent($question), $maxAnswerChars, ''))
                ->filter(static fn (string $answer): bool => $answer !== '')
                ->all(),
            currentSkill: $plan->skillName,
            difficulty: $plan->difficulty,
            isFollowUp: $plan->isFollowUp,
            focusConcepts: $plan->focusConcepts,
        );
    }

    /**
     * A question that is still being asked has no answer row yet, so the
     * relation is read defensively rather than assumed.
     */
    private function answerContent(InterviewQuestion $question): string
    {
        $answer = $question->getRelationValue('answer');

        return $answer instanceof Answer ? (string) $answer->content : '';
    }

    /**
     * The AI names a skill, but only a skill this position actually requires is
     * acceptable. Anything else means the model drifted, so the planned skill
     * is used instead of the suggestion.
     */
    private function resolveSkill(Interview $interview, NextQuestionPlan $plan, GeneratedQuestion $generated): Skill
    {
        $skill = Skill::where('name', $generated->skill)->first();

        if ($skill === null) {
            return Skill::whereKey($plan->skillId)->firstOrFail();
        }

        $isRequired = $interview->position->skills()->whereKey($skill->id)->exists();

        return $isRequired ? $skill : Skill::whereKey($plan->skillId)->firstOrFail();
    }

    /**
     * The AI may suggest a difficulty, but Laravel decides. A suggestion more
     * than one step from the plan is discarded, and an unrecognised suggestion
     * falls back to the plan.
     */
    private function resolveDifficulty(NextQuestionPlan $plan, GeneratedQuestion $generated): QuestionDifficulty
    {
        if ($this->withinOneStep($plan->difficulty, $generated->difficulty)) {
            return $generated->difficulty;
        }

        return $plan->difficulty;
    }

    private function withinOneStep(QuestionDifficulty $planned, QuestionDifficulty $suggested): bool
    {
        return abs($this->rank($planned) - $this->rank($suggested)) <= 1;
    }

    private function rank(QuestionDifficulty $difficulty): int
    {
        return match ($difficulty) {
            QuestionDifficulty::Easy => 1,
            QuestionDifficulty::Medium => 2,
            QuestionDifficulty::Hard => 3,
        };
    }

    /**
     * @param  array<string, int|string|null>  $invocation
     */
    private function persist(
        int $interviewId,
        NextQuestionPlan $plan,
        GeneratedQuestion $generated,
        Skill $skill,
        QuestionDifficulty $difficulty,
        array $invocation,
    ): InterviewQuestion {
        return DB::transaction(function () use ($interviewId, $generated, $skill, $difficulty, $invocation): InterviewQuestion {
            $interview = Interview::where('id', $interviewId)->lockForUpdate()->firstOrFail();

            if ($interview->hasReachedQuestionLimit()) {
                abort(422, 'This interview has already asked every question.');
            }

            $question = InterviewQuestion::create([
                'interview_id' => $interview->id,
                'question_id' => null,
                'position' => $interview->nextQuestionPosition(),
                'skill_id' => $skill->id,
                'difficulty' => $difficulty,
                'question_text' => $generated->question,
                'status' => QuestionStatus::Asking,
                'source' => QuestionSource::AI,
                'evaluation_criteria' => $generated->evaluationCriteria,
                'generation_metadata' => $invocation === [] ? null : $invocation,
                'asked_at' => now(),
            ]);

            $interview->update([
                'current_question_id' => $question->id,
                'question_index' => $interview->question_index + 1,
            ]);

            return $question;
        });
    }
}
