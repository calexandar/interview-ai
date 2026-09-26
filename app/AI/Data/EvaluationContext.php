<?php

namespace App\AI\Data;

use App\Shared\Enums\QuestionDifficulty;

/**
 * Everything the AI is allowed to know when evaluating an answer.
 *
 * The candidate answer is untrusted input. It is passed as a value to be
 * judged, never as an instruction to be followed.
 */
readonly class EvaluationContext
{
    /**
     * @param  array<int, string>  $evaluationCriteria
     * @param  array<int, string>  $requiredSkills
     */
    public function __construct(
        public string $question,
        public array $evaluationCriteria,
        public string $candidateAnswer,
        public string $jobTitle,
        public array $requiredSkills,
        public ?string $skill,
        public QuestionDifficulty $difficulty,
    ) {}
}
