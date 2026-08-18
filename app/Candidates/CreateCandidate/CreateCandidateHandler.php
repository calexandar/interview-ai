<?php

namespace App\Candidates\CreateCandidate;

use App\Models\Candidate;

class CreateCandidateHandler
{
    public function handle(CreateCandidate $command): Candidate
    {
        return Candidate::create([
            'organization_id' => $command->organizationId,
            'name' => $command->name,
            'email' => $command->email,
        ]);
    }
}
