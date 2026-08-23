<?php

namespace App\Interviewing\EndInterview;

readonly class EndInterview
{
    public function __construct(
        public int $interviewId,
        public int $organizationId,
    ) {}
}
