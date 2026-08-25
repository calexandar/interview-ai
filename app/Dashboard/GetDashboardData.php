<?php

namespace App\Dashboard;

readonly class GetDashboardData
{
    public function __construct(
        public int $organizationId,
    ) {}
}
