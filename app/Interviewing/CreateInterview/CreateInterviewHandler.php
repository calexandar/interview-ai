<?php

namespace App\Interviewing\CreateInterview;

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Position;
use App\Shared\Enums\InterviewStatus;
use Illuminate\Support\Facades\DB;

class CreateInterviewHandler
{
    public function handle(CreateInterview $command): Interview
    {
        $position = Position::where('id', $command->positionId)
            ->where('organization_id', $command->organizationId)
            ->firstOrFail();

        $candidate = Candidate::where('id', $command->candidateId)
            ->where('organization_id', $command->organizationId)
            ->firstOrFail();

        if ($position->status->value !== 'active') {
            abort(422, 'Position must be active to create an interview.');
        }

        $hasActiveInterview = Interview::where('candidate_id', $candidate->id)
            ->where('position_id', $position->id)
            ->whereIn('status', [InterviewStatus::Scheduled, InterviewStatus::InProgress])
            ->exists();

        if ($hasActiveInterview) {
            abort(422, 'Candidate already has an active interview for this position.');
        }

        return DB::transaction(function () use ($command, $position) {
            return Interview::create([
                'organization_id' => $command->organizationId,
                'position_id' => $command->positionId,
                'candidate_id' => $command->candidateId,
                'status' => InterviewStatus::Scheduled,
                'type' => $command->type,
                'total_questions' => $position->question_count,
                'question_index' => 0,
            ]);
        });
    }
}
