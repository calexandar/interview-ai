<?php

namespace App\Positions\CreatePosition;

use App\Shared\Enums\PositionLevel;
use App\Shared\Enums\PositionStatus;

readonly class CreatePosition
{
    public function __construct(
        public int $organizationId,
        public string $title,
        public ?string $description,
        public PositionLevel $level,
        public int $durationMinutes,
        public int $questionCount,
        public PositionStatus $status,
        public ?array $skillIds = null,
    ) {}
}
