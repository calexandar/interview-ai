<?php

namespace App\Interviewing\EndInterview;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EndInterviewController extends Controller
{
    public function __invoke(
        Request $request,
        EndInterviewHandler $handler,
    ): RedirectResponse {
        $handler->handle(new EndInterview(
            interviewId: (int) $request->route('interview'),
            organizationId: $request->user()->organization_id,
        ));

        return redirect()->route('dashboard');
    }
}
