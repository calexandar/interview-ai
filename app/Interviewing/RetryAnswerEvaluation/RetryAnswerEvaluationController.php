<?php

namespace App\Interviewing\RetryAnswerEvaluation;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RetryAnswerEvaluationController extends Controller
{
    public function __invoke(
        Request $request,
        RetryAnswerEvaluationHandler $handler,
    ): RedirectResponse {
        $answerId = (int) $request->input('answer_id');

        if ($answerId <= 0) {
            throw ValidationException::withMessages([
                'answer_id' => 'An answer is required to retry an evaluation.',
            ]);
        }

        $evaluation = $handler->handle(new RetryAnswerEvaluation(
            interviewId: (int) $request->route('interview'),
            answerId: $answerId,
            organizationId: $request->user()->organization_id,
        ));

        if ($evaluation === null) {
            return back()->with('warning', 'We could not review that answer just now. You can try again.');
        }

        return back();
    }
}
