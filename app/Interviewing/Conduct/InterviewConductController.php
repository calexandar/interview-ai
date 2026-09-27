<?php

namespace App\Interviewing\Conduct;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->with(['candidate', 'position', 'interviewQuestions.skill', 'interviewQuestions.answer.evaluation'])
            ->firstOrFail();

        $activeQuestion = $interviewModel->activeQuestion();

        // An aggregate, not a model, so it is run through the query builder
        // rather than the Eloquent relation.
        $sections = DB::table('interview_questions')
            ->where('interview_id', $interviewModel->id)
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

        return Inertia::render('Interview/Conduct', [
            'interview' => [
                'id' => $interviewModel->id,
                'status' => $interviewModel->status->value,
                'startedAt' => $interviewModel->started_at?->toIso8601String(),
                'durationSeconds' => $interviewModel->durationSeconds(),
                'remainingSeconds' => $interviewModel->remainingSeconds(),
                'questionIndex' => $interviewModel->question_index,
                'totalQuestions' => $interviewModel->total_questions,
                // Derived server-side so the page never has to re-implement the
                // rule for which statuses may be started.
                'canStart' => $interviewModel->canBeStarted(),
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
                'answerId' => $this->answerIdFor($activeQuestion),
                'text' => $activeQuestion->question_text,
                // The skill is resolved from the interview question, not from
                // the question bank, because AI generated questions have no
                // bank question attached. skill_id is a non-nullable foreign
                // key, so the skill always resolves.
                'skill' => $activeQuestion->skill->name,
                'difficulty' => $activeQuestion->difficulty->value,
                'status' => $activeQuestion->status->value,
                'evaluationState' => $activeQuestion->evaluationState(),
            ] : null,
            'progress' => [
                'percentage' => $interviewModel->progressPercentage(),
                'answered' => $interviewModel->answeredCount(),
                'total' => $interviewModel->total_questions,
                'sections' => $sections,
            ],
        ]);
    }

    /**
     * The answer may not exist yet for a question that is still being asked.
     */
    private function answerIdFor(InterviewQuestion $question): ?int
    {
        $answer = $question->getRelationValue('answer');

        return $answer instanceof Answer ? $answer->id : null;
    }

    private function sectionStatus(int $completed, int $total): string
    {
        if ($total > 0 && $completed >= $total) {
            return 'completed';
        }

        return $completed > 0 ? 'in_progress' : 'pending';
    }
}
