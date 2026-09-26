<?php

namespace App\Interviewing\MoveToNextQuestion;

readonly class MoveToNextQuestion
{
    public function __construct(
        public int $interviewId,
        public int $organizationId,
    ) {}
}
