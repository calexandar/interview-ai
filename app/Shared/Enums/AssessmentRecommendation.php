<?php

namespace App\Shared\Enums;

enum AssessmentRecommendation: string
{
    case StrongHire = 'strong_hire';
    case Hire = 'hire';
    case Mixed = 'mixed';
    case NoHire = 'no_hire';
    case StrongNoHire = 'strong_no_hire';

    public function label(): string
    {
        return match ($this) {
            self::StrongHire => 'Strong Hire',
            self::Hire => 'Hire',
            self::Mixed => 'Mixed',
            self::NoHire => 'No Hire',
            self::StrongNoHire => 'Strong No Hire',
        };
    }
}
