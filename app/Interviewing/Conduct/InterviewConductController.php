<?php

namespace App\Interviewing\Conduct;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InterviewConductController extends Controller
{
    public function __invoke(
        Request $request,
        int $interview,
    ): Response {
        $interviewModel = Interview::where('id', $interview)
            ->where('organization_id', $request->user()->organization_id)
            ->with(['candidate', 'position.skills', 'interviewQuestions.question.skill', 'interviewQuestions.answer'])
            ->firstOrFail();

        $activeQuestion = $interviewModel->activeQuestion()?->load('question.skill');

        $sections = $interviewModel->interviewQuestions()
            ->leftJoin('skills', 'skills.id', '=', 'interview_questions.skill_id')
            ->selectRaw("COALESCE(skills.name, 'General') as section_name")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN interview_questions.status = ? THEN 1 ELSE 0 END) as completed', [QuestionStatus::Answered->value])
            ->groupBy('section_name')
            ->orderBy('section_name')
            ->get()
            ->map(fn ($row): array => [
                'name' => (string) $row->section_name,
                'completed' => (int) $row->completed,
                'total' => (int) $row->total,
                'status' => $this->sectionStatus((int) $row->completed, (int) $row->total),
            ])
            ->values()
            ->all();

        return Inertia::render('Interview/Conduct', [
            'interview' => [
                'id' => $interviewModel->id,
                'status' => $interviewModel->status->value,
                'startedAt' => $interviewModel->started_at?->toIso8601String(),
                'durationSeconds' => $interviewModel->durationSeconds(),
                'remainingSeconds' => $interviewModel->remainingSeconds(),
                'questionIndex' => $interviewModel->question_index,
                'totalQuestions' => $interviewModel->total_questions,
            ],
            'candidate' => [
                'id' => $interviewModel->candidate->id,
                'name' => $interviewModel->candidate->name,
            ],
            'position' => [
                'id' => $interviewModel->position->id,
                'title' => $interviewModel->position->title,
            ],
            'currentQuestion' => $activeQuestion ? [
                'id' => $activeQuestion->id,
                'text' => $activeQuestion->question_text,
                'skill' => $activeQuestion->question?->skill?->name ?? 'General',
                'difficulty' => $activeQuestion->difficulty->value,
                'status' => $activeQuestion->status->value,
            ] : null,
            'progress' => [
                'percentage' => $interviewModel->progressPercentage(),
                'answered' => $interviewModel->answeredCount(),
                'total' => $interviewModel->total_questions,
                'sections' => $sections,
            ],
        ]);
    }

    private function sectionStatus(int $completed, int $total): string
    {
        if ($total > 0 && $completed >= $total) {
            return 'completed';
        }

        return $completed > 0 ? 'in_progress' : 'pending';
    }
}
