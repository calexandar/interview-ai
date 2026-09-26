<?php

namespace App\Interviewing\StartInterview;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StartInterviewController extends Controller
{
    public function __invoke(
        Request $request,
        StartInterviewHandler $handler,
    ): RedirectResponse {
        $handler->handle(new StartInterview(
            interviewId: (int) $request->route('interview'),
            organizationId: $request->user()->organization_id,
        ));

        return redirect()->route('interviews.conduct', $request->route('interview'));
    }
}
