<?php

namespace App\Shared\Enums;

enum QuestionStatus: string
{
    case Pending = 'pending';
    case Asked = 'asked';
    case Answered = 'answered';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Asked => 'Asked',
            self::Answered => 'Answered',
        };
    }
}
