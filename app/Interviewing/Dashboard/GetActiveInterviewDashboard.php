<?php

namespace App\Interviewing\Dashboard;

readonly class GetActiveInterviewDashboard
{
    public function __construct(
        public int $organizationId,
    ) {}
}
