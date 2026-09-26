<?php

namespace App\Interviewing\UpdateSkillAssessment;

readonly class UpdateSkillAssessment
{
    public function __construct(
        public int $interviewId,
        public int $skillId,
    ) {}
}
