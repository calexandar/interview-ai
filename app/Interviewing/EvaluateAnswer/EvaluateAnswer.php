<?php

namespace App\Interviewing\EvaluateAnswer;

readonly class EvaluateAnswer
{
    public function __construct(
        public int $answerId,
        public int $interviewId,
        public int $organizationId,
    ) {}
}
