<?php

namespace App\Positions\Dashboard;

use App\Models\Assessment;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Position;
use App\Shared\Enums\AssessmentRecommendation;
use Illuminate\Support\Collection;

class GetDashboardDataHandler
{
    public function handle(GetDashboardData $command): array
    {
        $orgId = $command->organizationId;

        $activePositionsCount = Position::where('organization_id', $orgId)
            ->where('status', 'active')
            ->count();

        $candidatesCount = Candidate::where('organization_id', $orgId)
            ->count();

        $interviewsCount = Interview::where('organization_id', $orgId)
            ->count();

        $strongCandidatesCount = Assessment::whereHas('interview', function ($query) use ($orgId) {
            $query->where('organization_id', $orgId);
        })
            ->whereIn('recommendation', [
                AssessmentRecommendation::StrongHire->value,
                AssessmentRecommendation::Hire->value,
            ])
            ->count();

        $recentInterviews = $this->getRecentInterviews($orgId);

        return [
            'activePositionsCount' => $activePositionsCount,
            'candidatesCount' => $candidatesCount,
            'interviewsCount' => $interviewsCount,
            'strongCandidatesCount' => $strongCandidatesCount,
            'recentInterviews' => $recentInterviews,
        ];
    }

    private function getRecentInterviews(int $orgId): Collection
    {
        return Interview::where('organization_id', $orgId)
            ->with(['candidate', 'position', 'assessment'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function (Interview $interview) {
                /** @var Candidate $candidate */
                $candidate = $interview->candidate;
                /** @var Position $position */
                $position = $interview->position;
                /** @var Assessment|null $assessment */
                $assessment = $interview->assessment;

                return [
                    'id' => $interview->id,
                    'candidate_name' => $candidate->name,
                    'position_title' => $position->title,
                    'status' => (string) $interview->status->value,
                    'score' => $assessment?->overall_score,
                    'recommendation' => $assessment?->recommendation?->value,
                    'created_at' => $interview->created_at->toIso8601String(),
                ];
            });
    }
}
