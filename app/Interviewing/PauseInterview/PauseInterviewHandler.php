<?php

namespace App\Interviewing\PauseInterview;

use App\Models\Interview;
use App\Shared\Enums\InterviewStatus;
use Illuminate\Support\Facades\DB;

class PauseInterviewHandler
{
    public function handle(PauseInterview $command): Interview
    {
        return DB::transaction(function () use ($command) {
            $interview = Interview::where('id', $command->interviewId)
                ->where('organization_id', $command->organizationId)
                ->firstOrFail();

            if (! $interview->isInProgress()) {
                abort(422, 'Only in-progress interviews can be paused.');
            }

            $interview->update([
                'status' => InterviewStatus::Paused,
                'paused_at' => now(),
            ]);

            return $interview;
        });
    }
}
