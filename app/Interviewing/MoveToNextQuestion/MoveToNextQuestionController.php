<?php

namespace App\Interviewing\MoveToNextQuestion;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MoveToNextQuestionController extends Controller
{
    public function __invoke(
        Request $request,
        MoveToNextQuestionHandler $handler,
    ): RedirectResponse {
        $result = $handler->handle(new MoveToNextQuestion(
            interviewId: (int) $request->route('interview'),
            organizationId: $request->user()->organization_id,
        ));

        if ($result['completed']) {
            return redirect()->route('dashboard');
        }

        return back();
    }
}
