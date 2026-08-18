<?php

namespace App\Candidates\CreateCandidate;

readonly class CreateCandidate
{
    public function __construct(
        public int $organizationId,
        public string $name,
        public string $email,
    ) {}
}
