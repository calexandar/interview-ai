<?php

namespace App\Interviewing\SubmitAnswer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubmitAnswerController extends Controller
{
    public function __invoke(
        Request $request,
        SubmitAnswerHandler $handler,
    ): RedirectResponse {
        $handler->handle(new SubmitAnswer(
            interviewId: (int) $request->route('interview'),
            questionId: (int) $request->input('question_id'),
            candidateId: $request->user()->candidate_id ?? $request->input('candidate_id'),
            organizationId: $request->user()->organization_id,
            content: $request->input('content'),
            durationSeconds: (int) $request->input('duration_seconds', 0),
        ));

        return back()->with('success', 'Answer submitted successfully.');
    }
}
