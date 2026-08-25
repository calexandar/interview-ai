<?php

namespace App\Dashboard;

readonly class GetActiveInterviewDashboard
{
    public function __construct(
        public int $organizationId,
    ) {}
}
