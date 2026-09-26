<?php

namespace App\Interviewing\SkipQuestion;

readonly class SkipQuestion
{
    public function __construct(
        public int $interviewId,
        public int $questionId,
        public int $organizationId,
    ) {}
}
