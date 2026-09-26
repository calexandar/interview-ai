<?php

namespace App\Dashboard;

use App\Models\Assessment;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionStatus;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetActiveInterviewDashboardHandler
{
    /**
     * Assembles the interview-focused dashboard payload.
     *
     * Only domain-derived data is returned. Concepts that are not yet
     * modelled (interview phases, AI agent state) are left to the
     * presentation layer instead of being fabricated here.
     *
     * @return array{
     *     activeInterview: array{
     *         id: int,
     *         candidateName: string,
     *         jobTitle: string,
     *         status: 'cancelled'|'completed'|'draft'|'expired'|'in_progress'|'paused'|'scheduled',
     *         startedAt: string,
     *         durationSeconds: int,
     *         remainingSeconds: int,
     *     }|null,
     *     currentQuestion: array{text: string, state: string}|null,
     *     progress: array{percentage: int, sections: array<int, array{name: string, completed: int, total: int, status: string}>},
     *     skills: array<int, array{skill: string, score: int}>,
     *     statistics: array{interviews: array{value: int, trend: array{value: int, direction: string}|null}, completed: array{value: int, trend: array{value: int, direction: string}|null}, inProgress: array{value: int, trend: array{value: int, direction: string}|null}, averageScorePercent: array{value: int|null, trend: null}},
     *     interviewsOverTime: array<int, array{label: string, count: int}>,
     * }
     */
    public function handle(GetActiveInterviewDashboard $command): array
    {
        $orgId = $command->organizationId;
        $interview = $this->findActiveInterview($orgId);

        return [
            'activeInterview' => $this->describeInterview($interview),
            'currentQuestion' => $this->getCurrentQuestion($interview),
            'progress' => $this->getProgress($interview),
            'skills' => $this->getSkills($interview),
            'statistics' => [
                'interviews' => [
                    'value' => $this->weeklyCount($orgId),
                    'trend' => $this->weeklyTrend($orgId),
                ],
                'completed' => [
                    'value' => $this->weeklyCount($orgId, InterviewStatus::Completed),
                    'trend' => $this->weeklyTrend($orgId, InterviewStatus::Completed),
                ],
                'inProgress' => [
                    'value' => $this->weeklyCount($orgId, InterviewStatus::InProgress),
                    'trend' => $this->weeklyTrend($orgId, InterviewStatus::InProgress),
                ],
                'averageScorePercent' => [
                    'value' => $this->averageScorePercent($orgId),
                    'trend' => null,
                ],
            ],
            'interviewsOverTime' => $this->getInterviewsOverTime($orgId),
        ];
    }

    private function findActiveInterview(int $orgId): ?Interview
    {
        /** @var Interview|null */
        return Interview::where('organization_id', $orgId)
            ->where('status', InterviewStatus::InProgress)
            ->with(['candidate', 'position'])
            ->latest('started_at')
            ->first();
    }

    /**
     * @return array{id: int, candidateName: string, jobTitle: string, status: 'cancelled'|'completed'|'draft'|'expired'|'in_progress'|'paused'|'scheduled', startedAt: string, durationSeconds: int, remainingSeconds: int}|null
     */
    private function describeInterview(?Interview $interview): ?array
    {
        if ($interview === null) {
            return null;
        }

        $durationSeconds = $interview->position->duration_minutes * 60;
        $elapsedSeconds = (int) $interview->started_at->diffInSeconds(now());
        $remainingSeconds = max(0, $durationSeconds - $elapsedSeconds);

        return [
            'id' => $interview->id,
            'candidateName' => $interview->candidate->name,
            'jobTitle' => $interview->position->title,
            'status' => $interview->status->value,
            'startedAt' => $interview->started_at->toIso8601String(),
            'durationSeconds' => $durationSeconds,
            'remainingSeconds' => $remainingSeconds,
        ];
    }

    /**
     * @return array{text: string, state: string}|null
     */
    private function getCurrentQuestion(?Interview $interview): ?array
    {
        if ($interview === null) {
            return null;
        }

        /** @var InterviewQuestion|null $interviewQuestion */
        $interviewQuestion = $interview->currentQuestion()->with('question')->first();

        if ($interviewQuestion?->question === null) {
            return null;
        }

        return [
            'text' => $interviewQuestion->question->question,
            'state' => match ($interviewQuestion->status) {
                QuestionStatus::Asking => 'listening',
                QuestionStatus::Answering => 'listening',
                QuestionStatus::Processing => 'processing',
                QuestionStatus::Answered => 'answered',
                QuestionStatus::Skipped => 'answered',
                QuestionStatus::Pending => 'pending',
            },
        ];
    }

    /**
     * @return array{percentage: int, sections: array<int, array{name: string, completed: int, total: int, status: string}>}
     */
    private function getProgress(?Interview $interview): array
    {
        if ($interview === null) {
            return ['percentage' => 0, 'sections' => []];
        }

        $percentage = $interview->total_questions > 0
            ? (int) floor(($interview->question_index / $interview->total_questions) * 100)
            : 0;

        $sections = DB::table('interview_questions')
            ->where('interview_id', $interview->id)
            ->leftJoin('skills', 'skills.id', '=', 'interview_questions.skill_id')
            ->selectRaw("COALESCE(skills.name, 'General') as section_name")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN interview_questions.status = ? THEN 1 ELSE 0 END) as completed', [QuestionStatus::Answered->value])
            ->groupBy('section_name')
            ->orderBy('section_name')
            ->get()
            ->map(fn (object $row): array => [
                'name' => (string) $row->section_name,
                'completed' => (int) $row->completed,
                'total' => (int) $row->total,
                'status' => $this->sectionStatus((int) $row->completed, (int) $row->total),
            ])
            ->values()
            ->all();

        return [
            'percentage' => $percentage,
            'sections' => $sections,
        ];
    }

    /**
     * @return array<int, array{skill: string, score: int}>
     */
    private function getSkills(?Interview $interview): array
    {
        if ($interview === null) {
            return [];
        }

        return DB::table('skill_assessments')
            ->where('interview_id', $interview->id)
            ->join('skills', 'skills.id', '=', 'skill_assessments.skill_id')
            ->orderBy('skills.name')
            ->get(['skills.name as skill_name', 'skill_assessments.score'])
            ->map(fn (object $row): array => [
                'skill' => (string) $row->skill_name,
                'score' => (int) round(((float) $row->score) * 10),
            ])
            ->values()
            ->all();
    }

    private function weeklyCount(int $orgId, ?InterviewStatus $status = null): int
    {
        return Interview::where('organization_id', $orgId)
            ->whereBetween('created_at', [$this->weekStart(), now()])
            ->when($status !== null, fn ($query) => $query->where('status', $status))
            ->count();
    }

    private function averageScorePercent(int $orgId): ?int
    {
        $average = Assessment::whereHas('interview', fn ($query) => $query->where('organization_id', $orgId))
            ->whereBetween('assessments.created_at', [$this->weekStart(), now()])
            ->avg('overall_score');

        return $average === null ? null : (int) round(((float) $average) * 10);
    }

    /**
     * Interview counts for the last seven days, oldest first.
     *
     * @return array<int, array{label: string, count: int}>
     */
    private function getInterviewsOverTime(int $orgId): array
    {
        $countsByDay = Interview::where('organization_id', $orgId)
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()])
            ->get()
            ->groupBy(fn (Interview $interview): string => $interview->created_at->format('Y-m-d'))
            ->map(fn (Collection $group): int => $group->count());

        return collect(range(6, 0))
            ->map(function (int $daysAgo) use ($countsByDay): array {
                $day = now()->subDays($daysAgo);

                return [
                    'label' => $day->format('D'),
                    'count' => $countsByDay->get($day->format('Y-m-d'), 0),
                ];
            })
            ->values()
            ->all();
    }

    private function sectionStatus(int $completed, int $total): string
    {
        if ($total > 0 && $completed >= $total) {
            return 'completed';
        }

        return $completed > 0 ? 'in_progress' : 'pending';
    }

    /**
     * @return array{value: int, direction: string}|null
     */
    private function weeklyTrend(int $orgId, ?InterviewStatus $status = null): ?array
    {
        return $this->weeklyTrendForCount(
            $this->weeklyCount($orgId, $status),
        );
    }

    /**
     * @return array{value: int, direction: string}|null
     */
    private function weeklyTrendForCount(int $count): ?array
    {
        if ($count === 0) {
            return null;
        }

        return [
            'value' => $count,
            'direction' => 'up',
        ];
    }

    private function weekStart(): CarbonInterface
    {
        return now()->startOfWeek();
    }
}
