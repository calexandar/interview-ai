<?php

namespace App\Interviewing\GetCurrentQuestion;

readonly class GetCurrentQuestion
{
    public function __construct(
        public int $interviewId,
        public int $organizationId,
    ) {}
}
