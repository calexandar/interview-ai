<?php

namespace App\Interviewing\ResumeInterview;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResumeInterviewController extends Controller
{
    public function __invoke(
        Request $request,
        ResumeInterviewHandler $handler,
    ): RedirectResponse {
        $handler->handle(new ResumeInterview(
            interviewId: (int) $request->route('interview'),
            organizationId: $request->user()->organization_id,
        ));

        return back();
    }
}
