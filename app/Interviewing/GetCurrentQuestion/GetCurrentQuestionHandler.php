<?php

namespace App\Interviewing\GetCurrentQuestion;

use App\Models\Interview;
use App\Models\InterviewQuestion;

class GetCurrentQuestionHandler
{
    /**
     * @return array{question: InterviewQuestion|null, progress: array{percentage: int, answered: int, total: int}}
     */
    public function handle(GetCurrentQuestion $command): array
    {
        $interview = Interview::where('id', $command->interviewId)
            ->where('organization_id', $command->organizationId)
            ->firstOrFail();

        $activeQuestion = $interview->activeQuestion();

        return [
            'question' => $activeQuestion,
            'progress' => [
                'percentage' => $interview->progressPercentage(),
                'answered' => $interview->answeredCount(),
                'total' => $interview->total_questions,
            ],
        ];
    }
}
