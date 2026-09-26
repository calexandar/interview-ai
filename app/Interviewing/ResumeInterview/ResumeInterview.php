<?php

namespace App\Interviewing\ResumeInterview;

readonly class ResumeInterview
{
    public function __construct(
        public int $interviewId,
        public int $organizationId,
    ) {}
}
