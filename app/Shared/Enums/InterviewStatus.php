<?php

namespace App\Shared\Enums;

enum InterviewStatus: string
{
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Paused = 'paused';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::InProgress => 'In Progress',
            self::Paused => 'Paused',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
