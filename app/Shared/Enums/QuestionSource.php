<?php

namespace App\Shared\Enums;

enum QuestionSource: string
{
    case QuestionBank = 'question_bank';
    case AI = 'ai';

    public function label(): string
    {
        return match ($this) {
            self::QuestionBank => 'Question Bank',
            self::AI => 'AI Generated',
        };
    }

    public function isAiGenerated(): bool
    {
        return $this === self::AI;
    }
}
