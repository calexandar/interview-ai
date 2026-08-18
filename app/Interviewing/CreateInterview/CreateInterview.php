<?php

namespace App\Interviewing\CreateInterview;

use App\Shared\Enums\InterviewType;

readonly class CreateInterview
{
    public function __construct(
        public int $organizationId,
        public int $positionId,
        public int $candidateId,
        public InterviewType $type,
    ) {}
}
