<?php

namespace App\Interviewing\PauseInterview;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PauseInterviewController extends Controller
{
    public function __invoke(
        Request $request,
        PauseInterviewHandler $handler,
    ): RedirectResponse {
        $handler->handle(new PauseInterview(
            interviewId: (int) $request->route('interview'),
            organizationId: $request->user()->organization_id,
        ));

        return back();
    }
}
