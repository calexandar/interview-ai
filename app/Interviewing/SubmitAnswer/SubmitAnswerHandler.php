<?php

namespace App\Interviewing\SubmitAnswer;

use App\Models\Answer;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Support\Facades\DB;

class SubmitAnswerHandler
{
    public function handle(SubmitAnswer $command): Answer
    {
        return DB::transaction(function () use ($command) {
            $interview = Interview::where('id', $command->interviewId)
                ->where('organization_id', $command->organizationId)
                ->firstOrFail();

            if (! $interview->isActive()) {
                abort(422, 'This interview is not active.');
            }

            if ($interview->hasExpired()) {
                $interview->update(['status' => InterviewStatus::Expired]);
                abort(422, 'This interview has expired.');
            }

            $question = InterviewQuestion::where('id', $command->questionId)
                ->where('interview_id', $interview->id)
                ->firstOrFail();

            if (! in_array($question->status, [QuestionStatus::Asking, QuestionStatus::Answering])) {
                abort(422, 'This question is not currently answerable.');
            }

            $existingAnswer = Answer::where('interview_question_id', $question->id)->first();
            if ($existingAnswer !== null) {
                abort(422, 'An answer has already been submitted for this question.');
            }

            $answer = Answer::create([
                'interview_question_id' => $question->id,
                'candidate_id' => $command->candidateId,
                'content' => $command->content,
                'submitted_at' => now(),
            ]);

            $question->update([
                'status' => QuestionStatus::Answered,
                'answered_at' => now(),
            ]);

            $interview->update([
                'question_index' => $interview->question_index + 1,
            ]);

            return $answer;
        });
    }
}
