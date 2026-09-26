<?php

namespace App\Interviewing\RetryAnswerEvaluation;

readonly class RetryAnswerEvaluation
{
    public function __construct(
        public int $interviewId,
        public int $answerId,
        public int $organizationId,
    ) {}
}
