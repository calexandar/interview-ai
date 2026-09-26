<?php

namespace App\Shared\Enums;

enum InterviewStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Paused = 'paused';
    case Completed = 'completed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Scheduled => 'Scheduled',
            self::InProgress => 'In Progress',
            self::Paused => 'Paused',
            self::Completed => 'Completed',
            self::Expired => 'Expired',
            self::Cancelled => 'Cancelled',
        };
    }

    public function canStart(): bool
    {
        return $this === self::Draft || $this === self::Scheduled;
    }

    public function isActive(): bool
    {
        return $this === self::InProgress || $this === self::Paused;
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Expired, self::Cancelled]);
    }
}
