<?php

namespace App\Interviewing\DetermineNextQuestion;

readonly class DetermineNextQuestion
{
    public function __construct(
        public int $interviewId,
        public int $organizationId,
    ) {}
}
