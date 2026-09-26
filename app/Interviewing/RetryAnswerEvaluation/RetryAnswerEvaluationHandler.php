<?php

namespace App\Interviewing\RetryAnswerEvaluation;

use App\Interviewing\EvaluateAnswer\EvaluateAnswerHandler;
use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\Interview;
use App\Shared\Enums\QuestionStatus;

/**
 * Re-runs an evaluation that failed or was never completed.
 *
 * Guarded rather than open: an evaluation can only be retried for an answer
 * that actually belongs to the interview, whose question was genuinely asked,
 * and which has no usable result yet. Because the underlying write is an
 * upsert, firing a retry twice is harmless.
 */
class RetryAnswerEvaluationHandler
{
    public function __construct(
        private EvaluateAnswerHandler $evaluations,
    ) {}

    /**
     * Returns the evaluation when the retry succeeded, or null when the
     * provider was unavailable and the failure was recorded instead.
     */
    public function handle(RetryAnswerEvaluation $command): ?Evaluation
    {
        $interview = Interview::where('id', $command->interviewId)
            ->where('organization_id', $command->organizationId)
            ->firstOrFail();

        if (! $interview->isActive()) {
            abort(422, 'This interview is not active.');
        }

        $answer = Answer::where('id', $command->answerId)
            ->whereHas('interviewQuestion', function ($query) use ($interview): void {
                $query->where('interview_id', $interview->id)
                    ->whereIn('status', [QuestionStatus::Answered, QuestionStatus::Skipped]);
            })
            ->firstOrFail();

        $evaluation = $answer->evaluation;

        if ($evaluation?->isCompleted() === true) {
            abort(422, 'This answer has already been evaluated.');
        }

        return $this->evaluations->attempt(
            interviewId: $interview->id,
            answerId: $answer->id,
            organizationId: $command->organizationId,
        );
    }
}
