<?php

namespace App\Shared\Enums;

enum PositionLevel: string
{
    case Junior = 'junior';
    case Mid = 'mid';
    case Senior = 'senior';
    case Lead = 'lead';

    public function label(): string
    {
        return match ($this) {
            self::Junior => 'Junior',
            self::Mid => 'Mid-Level',
            self::Senior => 'Senior',
            self::Lead => 'Lead',
        };
    }
}
