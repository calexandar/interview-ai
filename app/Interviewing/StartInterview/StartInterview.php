<?php

namespace App\Interviewing\StartInterview;

readonly class StartInterview
{
    public function __construct(
        public int $interviewId,
        public int $organizationId,
    ) {}
}
