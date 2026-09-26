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
        $interview = Interview::where('id', $command->interviewId)
            ->where('organization_id', $command->organizationId)
            ->firstOrFail();

        // The interview is loaded once up front only to report expiry. Anything
        // that writes has to happen outside the transaction below, because the
        // abort that follows would otherwise roll the expiry back and leave a
        // lapsed interview looking active.
        if ($interview->hasExpired()) {
            $interview->update(['status' => InterviewStatus::Expired]);

            abort(422, 'This interview has expired.');
        }

        return DB::transaction(function () use ($command) {
            $interview = Interview::where('id', $command->interviewId)
                ->where('organization_id', $command->organizationId)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $interview->isActive()) {
                abort(422, 'This interview is not active.');
            }

            $question = InterviewQuestion::where('id', $command->questionId)
                ->where('interview_id', $interview->id)
                ->firstOrFail();

            if (! in_array($question->status, [QuestionStatus::Asking, QuestionStatus::Answering], true)) {
                abort(422, 'This question is not currently answerable.');
            }

            if (Answer::where('interview_question_id', $question->id)->exists()) {
                abort(422, 'An answer has already been submitted for this question.');
            }

            // The candidate belongs to the interview, never to the request body.
            $answer = Answer::create([
                'interview_question_id' => $question->id,
                'candidate_id' => $interview->candidate_id,
                'content' => $command->content,
                'duration_seconds' => $command->durationSeconds,
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
