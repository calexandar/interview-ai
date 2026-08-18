<?php

namespace App\Shared\Enums;

enum QuestionDifficulty: string
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';

    public function label(): string
    {
        return match ($this) {
            self::Easy => 'Easy',
            self::Medium => 'Medium',
            self::Hard => 'Hard',
        };
    }

    public function increase(): self
    {
        return match ($this) {
            self::Easy => self::Medium,
            self::Medium => self::Hard,
            self::Hard => self::Hard,
        };
    }

    public function decrease(): self
    {
        return match ($this) {
            self::Easy => self::Easy,
            self::Medium => self::Easy,
            self::Hard => self::Medium,
        };
    }
}
