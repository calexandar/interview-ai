<?php

namespace App\Interviewing\MoveToNextQuestion;

use App\AI\Exceptions\AIUnavailableException;
use App\Interviewing\GenerateQuestion\GenerateQuestion;
use App\Interviewing\GenerateQuestion\GenerateQuestionHandler;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

/**
 * Closes out the current question and moves the interview on to the next one.
 *
 * Laravel owns the decision that the interview is over: completion is the
 * question count reaching the configured total, not the AI's opinion. The AI
 * supplies the next question; if it cannot, a pre-selected question-bank
 * question is used instead, so the interview never stalls.
 */
class MoveToNextQuestionHandler
{
    public function __construct(
        private GenerateQuestionHandler $questions,
        private LoggerInterface $logger,
    ) {}

    /**
     * @return array{question: InterviewQuestion|null, completed: bool}
     */
    public function handle(MoveToNextQuestion $command): array
    {
        $interview = Interview::where('id', $command->interviewId)
            ->where('organization_id', $command->organizationId)
            ->firstOrFail();

        if (! $interview->isActive()) {
            abort(422, 'This interview is not active.');
        }

        $this->resolveCurrentQuestion($interview);

        if ($interview->hasReachedQuestionLimit()) {
            return ['question' => $this->complete($interview), 'completed' => true];
        }

        $question = $this->askNextQuestion($interview) ?? $this->askFallbackQuestion($interview);

        if ($question === null) {
            return ['question' => $this->complete($interview), 'completed' => true];
        }

        return ['question' => $question->fresh(), 'completed' => false];
    }

    /**
     * Marks the question the candidate was last shown as answered.
     *
     * Submitting an answer already does this, so in the normal flow this is a
     * no-op. It matters when a question is moved past without an answer, and it
     * keeps the endpoint safe to call twice.
     */
    private function resolveCurrentQuestion(Interview $interview): void
    {
        $current = $interview->currentQuestion()->first();

        if ($current === null || ! in_array($current->status, [
            QuestionStatus::Asking,
            QuestionStatus::Answering,
        ], true)) {
            return;
        }

        $current->update([
            'status' => QuestionStatus::Answered,
            'answered_at' => $current->answered_at ?? now(),
        ]);

        $interview->update([
            'question_index' => $interview->question_index + 1,
        ]);
    }

    private function askNextQuestion(Interview $interview): ?InterviewQuestion
    {
        try {
            return $this->questions->handle(new GenerateQuestion(
                interviewId: $interview->id,
                organizationId: $interview->organization_id,
            ));
        } catch (AIUnavailableException $failure) {
            $this->logger->warning('interview.question_generation_failed', [
                'interview_id' => $interview->id,
                'error_category' => $failure->category,
            ]);

            return null;
        }
    }

    /**
     * Uses a question-bank question that was pre-selected for this interview and
     * never asked. This is the path that keeps an interview running through a
     * provider outage.
     */
    private function askFallbackQuestion(Interview $interview): ?InterviewQuestion
    {
        $question = $interview->nextPendingBankQuestion();

        if ($question === null) {
            return null;
        }

        return DB::transaction(function () use ($interview, $question): InterviewQuestion {
            $locked = InterviewQuestion::where('id', $question->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== QuestionStatus::Pending) {
                abort(422, 'This question is no longer available.');
            }

            $locked->update([
                'status' => QuestionStatus::Asking,
                'asked_at' => now(),
            ]);

            $interview->update([
                'current_question_id' => $locked->id,
                'question_index' => $interview->question_index + 1,
            ]);

            return $locked;
        });
    }

    private function complete(Interview $interview): null
    {
        $interview->update([
            'status' => InterviewStatus::Completed,
            'completed_at' => now(),
        ]);

        return null;
    }
}
