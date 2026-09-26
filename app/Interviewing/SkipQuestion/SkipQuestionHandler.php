<?php

namespace App\Interviewing\SkipQuestion;

use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Support\Facades\DB;

class SkipQuestionHandler
{
    public function handle(SkipQuestion $command): InterviewQuestion
    {
        return DB::transaction(function () use ($command) {
            $interview = Interview::where('id', $command->interviewId)
                ->where('organization_id', $command->organizationId)
                ->firstOrFail();

            if (! $interview->isActive()) {
                abort(422, 'This interview is not active.');
            }

            $question = InterviewQuestion::where('id', $command->questionId)
                ->where('interview_id', $interview->id)
                ->firstOrFail();

            if (! in_array($question->status, [QuestionStatus::Asking, QuestionStatus::Answering])) {
                abort(422, 'This question cannot be skipped.');
            }

            $question->update([
                'status' => QuestionStatus::Skipped,
            ]);

            $interview->update([
                'question_index' => $interview->question_index + 1,
            ]);

            return $question;
        });
    }
}
