<?php

namespace App\Interviewing\StartInterview;

use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Question;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Support\Facades\DB;

class StartInterviewHandler
{
    public function handle(StartInterview $command): Interview
    {
        return DB::transaction(function () use ($command) {
            $interview = Interview::where('id', $command->interviewId)
                ->where('organization_id', $command->organizationId)
                ->firstOrFail();

            if (! $interview->canBeStarted()) {
                abort(422, 'This interview cannot be started.');
            }

            $interview->update([
                'status' => InterviewStatus::InProgress,
                'started_at' => now(),
            ]);

            $this->selectQuestions($interview);
            $this->activateFirstQuestion($interview);

            return $interview->fresh();
        });
    }

    private function selectQuestions(Interview $interview): void
    {
        $position = $interview->position;
        $skills = $position->skills;

        $questionsPerSkill = max(1, (int) ceil($position->question_count / $skills->count()));

        $selectedQuestions = collect();

        foreach ($skills as $skill) {
            $skillQuestions = Question::where('skill_id', $skill->id)
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit($questionsPerSkill)
                ->get();

            $selectedQuestions = $selectedQuestions->concat($skillQuestions);
        }

        $selectedQuestions = $selectedQuestions->take($position->question_count)->values();

        foreach ($selectedQuestions as $index => $question) {
            InterviewQuestion::create([
                'interview_id' => $interview->id,
                'question_id' => $question->id,
                'position' => $index + 1,
                'skill_id' => $question->skill_id,
                'difficulty' => $question->difficulty,
                'question_text' => $question->question,
                'status' => QuestionStatus::Pending,
            ]);
        }

        $interview->update(['total_questions' => $selectedQuestions->count()]);
    }

    private function activateFirstQuestion(Interview $interview): void
    {
        $firstQuestion = $interview->interviewQuestions()
            ->orderBy('position')
            ->first();

        if ($firstQuestion === null) {
            return;
        }

        $firstQuestion->update([
            'status' => QuestionStatus::Asking,
            'asked_at' => now(),
        ]);

        $interview->update(['current_question_id' => $firstQuestion->id]);
    }
}
