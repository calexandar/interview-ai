<?php

namespace App\Positions\Dashboard;

readonly class GetDashboardData
{
    public function __construct(
        public int $organizationId,
    ) {}
}
