<?php

namespace App\Interviewing\MoveToNextQuestion;

use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Support\Facades\DB;

class MoveToNextQuestionHandler
{
    /**
     * @return array{question: InterviewQuestion|null, completed: bool}
     */
    public function handle(MoveToNextQuestion $command): array
    {
        return DB::transaction(function () use ($command) {
            $interview = Interview::where('id', $command->interviewId)
                ->where('organization_id', $command->organizationId)
                ->firstOrFail();

            if (! $interview->isActive()) {
                abort(422, 'This interview is not active.');
            }

            $nextQuestion = $interview->nextUnansweredQuestion();

            if ($nextQuestion === null) {
                $interview->update([
                    'status' => InterviewStatus::Completed,
                    'completed_at' => now(),
                ]);

                return ['question' => null, 'completed' => true];
            }

            $nextQuestion->update([
                'status' => QuestionStatus::Asking,
                'asked_at' => now(),
            ]);

            $interview->update([
                'current_question_id' => $nextQuestion->id,
            ]);

            return ['question' => $nextQuestion, 'completed' => false];
        });
    }
}
