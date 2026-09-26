<?php

namespace App\Interviewing\SubmitAnswer;

use App\Http\Controllers\Controller;
use App\Interviewing\EvaluateAnswer\EvaluateAnswerHandler;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class SubmitAnswerController extends Controller
{
    public function __invoke(
        SubmitAnswerRequest $request,
        SubmitAnswerHandler $handler,
        EvaluateAnswerHandler $evaluations,
    ): RedirectResponse {
        $command = $request->toCommand();

        $answer = $handler->handle($command);

        // The answer is already committed. Evaluating afterwards means a
        // provider outage delays a score but never costs a candidate their
        // answer, and the failure is recorded rather than surfaced as an error.
        $evaluation = $evaluations->attempt(
            interviewId: $command->interviewId,
            answerId: $answer->id,
            organizationId: $command->organizationId,
        );

        // A null evaluation means the provider let us down. The conduct page
        // stays on this question so the candidate is offered a retry rather
        // than being moved past an answer that was never reviewed.
        if ($evaluation === null) {
            return Inertia::flash('evaluation_failed', true)->back();
        }

        return back();
    }
}
