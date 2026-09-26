<?php

namespace App\Interviewing\StartInterview;

use App\AI\Exceptions\AIUnavailableException;
use App\Interviewing\GenerateQuestion\GenerateQuestion;
use App\Interviewing\GenerateQuestion\GenerateQuestionHandler;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Question;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionSource;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class StartInterviewHandler
{
    public function __construct(
        private GenerateQuestionHandler $questions,
        private LoggerInterface $logger,
    ) {}

    public function handle(StartInterview $command): Interview
    {
        $interview = DB::transaction(function () use ($command): Interview {
            $interview = Interview::where('id', $command->interviewId)
                ->where('organization_id', $command->organizationId)
                ->firstOrFail();

            if (! $interview->canBeStarted()) {
                abort(422, 'This interview cannot be started.');
            }

            $interview->update([
                'status' => InterviewStatus::InProgress,
                'started_at' => now(),
            ]);

            $this->selectFallbackQuestions($interview);

            return $interview->fresh();
        });

        $this->askFirstQuestion($interview);

        return $interview->fresh();
    }

    /**
     * Pre-selects question-bank questions for every required skill.
     *
     * These are a safety net, not the main path. The interview asks AI
     * generated questions, and a bank question is only used when the provider
     * cannot produce one. Unused bank questions simply stay Pending.
     *
     * Idempotent: an interview that already has a bank pool (because it was
     * populated when it was scheduled) keeps it, so starting twice can never
     * duplicate a question against the interview/question unique index.
     */
    private function selectFallbackQuestions(Interview $interview): void
    {
        $position = $interview->position;
        $skills = $position->skills;

        if ($skills->isEmpty()) {
            abort(422, 'This position has no skills configured.');
        }

        $interview->update(['total_questions' => (int) $position->question_count]);

        if ($interview->interviewQuestions()->exists()) {
            return;
        }

        $questionsPerSkill = max(1, (int) ceil($position->question_count / $skills->count()));

        $selected = collect();

        foreach ($skills as $skill) {
            $selected = $selected->concat(
                Question::where('skill_id', $skill->id)
                    ->where('is_active', true)
                    ->inRandomOrder()
                    ->limit($questionsPerSkill)
                    ->get(),
            );
        }

        $selected = $selected->take($position->question_count)->values();

        foreach ($selected as $index => $question) {
            InterviewQuestion::create([
                'interview_id' => $interview->id,
                'question_id' => $question->id,
                'position' => $index + 1,
                'skill_id' => $question->skill_id,
                'difficulty' => $question->difficulty,
                'question_text' => $question->question,
                'status' => QuestionStatus::Pending,
                'source' => QuestionSource::QuestionBank,
                'evaluation_criteria' => $question->expected_topics,
            ]);
        }
    }

    /**
     * Asks the opening question with the AI, falling back to the first bank
     * question if the provider is unavailable. A starting interview must never
     * fail because a third party is down.
     */
    private function askFirstQuestion(Interview $interview): void
    {
        try {
            $this->questions->handle(new GenerateQuestion(
                interviewId: $interview->id,
                organizationId: $interview->organization_id,
            ));

            return;
        } catch (AIUnavailableException $failure) {
            $this->logger->warning('interview.first_question_generation_failed', [
                'interview_id' => $interview->id,
                'error_category' => $failure->category,
            ]);
        }

        $this->activateFirstFallbackQuestion($interview);
    }

    private function activateFirstFallbackQuestion(Interview $interview): void
    {
        $question = $interview->nextPendingBankQuestion();

        if ($question === null) {
            return;
        }

        $question->update([
            'status' => QuestionStatus::Asking,
            'asked_at' => now(),
        ]);

        $interview->update(['current_question_id' => $question->id]);
    }
}
