<?php

namespace App\Positions\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        GetDashboardDataHandler $handler,
    ): Response {
        $data = $handler->handle(
            new GetDashboardData(
                organizationId: $request->user()->organization_id,
            ),
        );

        return Inertia::render('Dashboard', [
            'activePositionsCount' => $data['activePositionsCount'],
            'candidatesCount' => $data['candidatesCount'],
            'interviewsCount' => $data['interviewsCount'],
            'strongCandidatesCount' => $data['strongCandidatesCount'],
            'activePositionsTrend' => $data['activePositionsTrend'],
            'candidatesTrend' => $data['candidatesTrend'],
            'interviewsTrend' => $data['interviewsTrend'],
            'strongCandidatesTrend' => $data['strongCandidatesTrend'],
            'recentInterviews' => $data['recentInterviews'],
            'userName' => $request->user()->name,
        ]);
    }
}
