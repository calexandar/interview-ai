<?php

namespace App\Interviewing\PauseInterview;

readonly class PauseInterview
{
    public function __construct(
        public int $interviewId,
        public int $organizationId,
    ) {}
}
