<?php

namespace App\Shared\Enums;

enum QuestionStatus: string
{
    case Pending = 'pending';
    case Asking = 'asking';
    case Answering = 'answering';
    case Processing = 'processing';
    case Answered = 'answered';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Asking => 'Asking',
            self::Answering => 'Answering',
            self::Processing => 'Processing',
            self::Answered => 'Answered',
            self::Skipped => 'Skipped',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Asking, self::Answering, self::Processing]);
    }
}
