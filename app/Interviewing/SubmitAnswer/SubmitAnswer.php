<?php

namespace App\Interviewing\SubmitAnswer;

readonly class SubmitAnswer
{
    public function __construct(
        public int $interviewId,
        public int $questionId,
        public int $candidateId,
        public int $organizationId,
        public string $content,
        public int $durationSeconds = 0,
    ) {}
}
