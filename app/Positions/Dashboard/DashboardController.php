<?php

namespace App\Positions\Dashboard;

use App\Http\Controllers\Controller;
use App\Interviewing\Dashboard\GetActiveInterviewDashboard;
use App\Interviewing\Dashboard\GetActiveInterviewDashboardHandler;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        GetDashboardDataHandler $handler,
        GetActiveInterviewDashboardHandler $interviewHandler,
    ): Response {
        $data = $handler->handle(
            new GetDashboardData(
                organizationId: $request->user()->organization_id,
            ),
        );

        return Inertia::render('Dashboard/Index', [
            'recentInterviews' => $data['recentInterviews'],
            'userName' => $request->user()->name,

            'aiAgentStatus' => 'online',

            'session' => Inertia::defer(
                fn () => $interviewHandler->handle(
                    new GetActiveInterviewDashboard(
                        organizationId: $request->user()->organization_id,
                    ),
                ),
                rescue: true,
            ),
        ]);
    }
}
