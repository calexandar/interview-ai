<?php

namespace App\Interviewing\GenerateQuestion;

use App\AI\Exceptions\AIUnavailableException;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GenerateQuestionController extends Controller
{
    public function __invoke(
        Request $request,
        GenerateQuestionHandler $handler,
    ): RedirectResponse {
        try {
            $handler->handle(new GenerateQuestion(
                interviewId: (int) $request->route('interview'),
                organizationId: $request->user()->organization_id,
            ));
        } catch (AIUnavailableException) {
            return back()->with('warning', 'Could not generate a new question right now. Please try again.');
        }

        return back();
    }
}
