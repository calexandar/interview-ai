<?php

namespace App\Interviewing\EvaluateAnswer;

use App\AI\Contracts\InterviewAI;
use App\AI\Data\AnswerEvaluation;
use App\AI\Data\EvaluationContext;
use App\AI\Exceptions\AIUnavailableException;
use App\Interviewing\UpdateSkillAssessment\UpdateSkillAssessment;
use App\Interviewing\UpdateSkillAssessment\UpdateSkillAssessmentHandler;
use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Shared\Enums\EvaluationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Asks the AI to assess one submitted answer, then records the result.
 *
 * The answer is always already saved before this runs, so a provider failure
 * here can never cost a candidate their work: the answer stays, the evaluation
 * is marked failed, and the interview carries on. Re-running this handler is
 * safe, which is what makes retry possible.
 *
 * Deliberately does not touch question or interview status. Lifecycle
 * transitions belong to the actions that own them, not to the evaluator.
 */
class EvaluateAnswerHandler
{
    public function __construct(
        private InterviewAI $ai,
        private UpdateSkillAssessmentHandler $skillAssessments,
    ) {}

    /**
     * @throws AIUnavailableException
     */
    public function handle(EvaluateAnswer $command): Evaluation
    {
        $interview = Interview::where('id', $command->interviewId)
            ->where('organization_id', $command->organizationId)
            ->firstOrFail();

        $answer = Answer::where('id', $command->answerId)
            ->whereHas('interviewQuestion', fn ($query) => $query->where('interview_id', $interview->id))
            ->firstOrFail();

        $question = $answer->interviewQuestion()
            ->with(['skill', 'question', 'interview.position.skills'])
            ->firstOrFail();

        $result = $this->ai->evaluateAnswer($this->buildContext($answer, $question));

        $evaluation = $this->persist($answer, $result);

        $this->skillAssessments->handle(new UpdateSkillAssessment(
            interviewId: $interview->id,
            skillId: $question->skill_id,
        ));

        return $evaluation;
    }

    /**
     * Evaluates an answer, converting a provider failure into a recorded
     * failure instead of an exception.
     *
     * This is the entry point the interview lifecycle uses: an AI outage must
     * not surface as an error to a candidate who has already submitted their
     * work, so the attempt is recorded and the interview carries on. Returns
     * null when the attempt failed.
     */
    public function attempt(int $interviewId, int $answerId, int $organizationId): ?Evaluation
    {
        try {
            return $this->handle(new EvaluateAnswer(
                interviewId: $interviewId,
                answerId: $answerId,
                organizationId: $organizationId,
            ));
        } catch (AIUnavailableException $failure) {
            $answer = Answer::where('id', $answerId)->first();

            if ($answer !== null) {
                $this->recordFailure($answer, $failure);
            }

            Log::warning('interview.answer_evaluation_failed', [
                'interview_id' => $interviewId,
                'answer_id' => $answerId,
                'error_category' => $failure->category,
            ]);

            return null;
        }
    }

    /**
     * The answer reaches the model fenced inside explicit delimiters. The
     * criteria are included because the model needs the rubric, and nowhere
     * else: the conduct page never exposes them.
     */
    private function buildContext(Answer $answer, InterviewQuestion $question): EvaluationContext
    {
        $position = $question->interview->position;

        return new EvaluationContext(
            question: $question->question_text,
            evaluationCriteria: $question->evaluationCriteria(),
            candidateAnswer: Str::limit($answer->content, (int) config('interview.answer.max_evaluation_chars', 10000), ''),
            jobTitle: $position->title,
            requiredSkills: $position->skills->pluck('name')->all(),
            skill: $question->skill?->name,
            difficulty: $question->difficulty,
        );
    }

    private function persist(Answer $answer, AnswerEvaluation $result): Evaluation
    {
        return DB::transaction(function () use ($answer, $result): Evaluation {
            $existing = Evaluation::where('answer_id', $answer->id)->lockForUpdate()->first();

            $attributes = [
                'score' => $result->score,
                'confidence' => $result->confidence,
                'strengths' => $result->strengths,
                'weaknesses' => $result->weaknesses,
                'missing_topics' => $result->missingConcepts,
                'evidence' => $result->evidence,
                'follow_up_required' => $result->followUpRequired,
                'reasoning_summary' => $result->reasoningSummary,
                'status' => EvaluationStatus::Completed,
                'failure_reason' => null,
                'failed_at' => null,
            ];

            if ($existing !== null) {
                $existing->fill($attributes);
                $existing->attempts = $existing->attempts + 1;
                $existing->save();

                return $existing;
            }

            return Evaluation::create($attributes + [
                'answer_id' => $answer->id,
                'attempts' => 1,
            ]);
        });
    }

    /**
     * Records that an attempt failed, so an evaluation never silently goes
     * missing. Runs when handle() throws.
     *
     * The absence of a row already means "not evaluated", so nothing is
     * invented here: a score of zero would be a fabricated result. A good
     * evaluation is never demoted by a later failure either.
     */
    public function recordFailure(Answer $answer, AIUnavailableException $failure): void
    {
        $existing = Evaluation::where('answer_id', $answer->id)->first();

        if ($existing === null || $existing->isCompleted()) {
            return;
        }

        $existing->update([
            'status' => EvaluationStatus::Failed,
            'failure_reason' => $failure->category,
            'failed_at' => now(),
            'attempts' => $existing->attempts + 1,
        ]);
    }
}
