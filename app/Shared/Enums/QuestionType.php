<?php

namespace App\Shared\Enums;

enum QuestionType: string
{
    case Conceptual = 'conceptual';
    case Practical = 'practical';
    case Debugging = 'debugging';
    case Architecture = 'architecture';
    case Scenario = 'scenario';
    case CodeReview = 'code_review';

    public function label(): string
    {
        return match ($this) {
            self::Conceptual => 'Conceptual',
            self::Practical => 'Practical',
            self::Debugging => 'Debugging',
            self::Architecture => 'Architecture',
            self::Scenario => 'Scenario',
            self::CodeReview => 'Code Review',
        };
    }
}
