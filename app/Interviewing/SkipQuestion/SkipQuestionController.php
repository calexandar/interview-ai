<?php

namespace App\Interviewing\SkipQuestion;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SkipQuestionController extends Controller
{
    public function __invoke(
        Request $request,
        SkipQuestionHandler $handler,
    ): RedirectResponse {
        $handler->handle(new SkipQuestion(
            interviewId: (int) $request->route('interview'),
            questionId: (int) $request->input('question_id'),
            organizationId: $request->user()->organization_id,
        ));

        return back();
    }
}
