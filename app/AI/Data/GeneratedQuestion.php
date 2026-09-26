<?php

namespace App\AI\Data;

use App\Shared\Enums\QuestionDifficulty;

/**
 * A question proposed by the AI.
 *
 * This is a proposal, not a decision. Skill, difficulty and criteria are all
 * re-validated by Laravel before anything is persisted.
 */
readonly class GeneratedQuestion
{
    /**
     * @param  array<int, string>  $evaluationCriteria
     */
    public function __construct(
        public string $question,
        public ?string $skill,
        public QuestionDifficulty $difficulty,
        public array $evaluationCriteria,
    ) {}
}
