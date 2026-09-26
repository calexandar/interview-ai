<?php

namespace App\Shared\Enums;

enum EvaluationStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
        };
    }

    public function isSettled(): bool
    {
        return $this === self::Completed;
    }
}
