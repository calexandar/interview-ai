<?php

namespace App\Interviewing\DetermineNextQuestion;

use App\Shared\Enums\QuestionDifficulty;

/**
 * What the next question should target, decided entirely in PHP.
 *
 * The plan is a proposal handed to the AI, not an instruction the AI can
 * override. Every field here is authoritative and re-validated downstream.
 */
readonly class NextQuestionPlan
{
    /**
     * @param  array<int, string>  $focusConcepts
     */
    public function __construct(
        public int $skillId,
        public string $skillName,
        public QuestionDifficulty $difficulty,
        public bool $isFollowUp,
        public array $focusConcepts = [],
    ) {}
}
