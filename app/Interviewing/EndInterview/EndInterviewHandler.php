<?php

namespace App\Interviewing\EndInterview;

use App\Models\Interview;
use App\Shared\Enums\InterviewStatus;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EndInterviewHandler
{
    public function handle(EndInterview $command): Interview
    {
        return DB::transaction(function () use ($command) {
            $interview = Interview::where('id', $command->interviewId)
                ->where('organization_id', $command->organizationId)
                ->first();

            if ($interview === null || ! $interview->isInProgress()) {
                throw $this->notFound();
            }

            $interview->update([
                'status' => InterviewStatus::Completed,
                'completed_at' => now(),
            ]);

            return $interview;
        });
    }

    private function notFound(): HttpException
    {
        return new NotFoundHttpException('Interview not found.');
    }
}
