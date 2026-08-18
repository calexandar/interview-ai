<?php

namespace App\Shared\Enums;

enum InterviewType: string
{
    case Technical = 'technical';
    case Behavioral = 'behavioral';
    case Mixed = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::Technical => 'Technical',
            self::Behavioral => 'Behavioral',
            self::Mixed => 'Mixed',
        };
    }
}
