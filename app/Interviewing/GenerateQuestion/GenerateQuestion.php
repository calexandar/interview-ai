<?php

namespace App\Interviewing\GenerateQuestion;

use App\Interviewing\DetermineNextQuestion\NextQuestionPlan;

readonly class GenerateQuestion
{
    public function __construct(
        public int $interviewId,
        public int $organizationId,
        public ?NextQuestionPlan $plan = null,
    ) {}
}
