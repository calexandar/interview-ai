<?php

namespace App\Interviewing\ResumeInterview;

use App\Models\Interview;
use App\Shared\Enums\InterviewStatus;
use Illuminate\Support\Facades\DB;

class ResumeInterviewHandler
{
    public function handle(ResumeInterview $command): Interview
    {
        return DB::transaction(function () use ($command) {
            $interview = Interview::where('id', $command->interviewId)
                ->where('organization_id', $command->organizationId)
                ->firstOrFail();

            if (! $interview->isPaused()) {
                abort(422, 'Only paused interviews can be resumed.');
            }

            $pausedDuration = $interview->paused_at->diffInSeconds(now());

            $interview->update([
                'status' => InterviewStatus::InProgress,
                'paused_at' => null,
                'started_at' => $interview->started_at->addSeconds($pausedDuration),
            ]);

            return $interview;
        });
    }
}
