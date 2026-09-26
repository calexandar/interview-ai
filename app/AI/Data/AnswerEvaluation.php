<?php

namespace App\AI\Data;

/**
 * An assessment of a single answer.
 *
 * Deliberately one overall score plus evidence. Score aggregation across a
 * skill or an interview is computed in PHP, never requested from the model, so
 * results stay reproducible and explainable.
 */
readonly class AnswerEvaluation
{
    /**
     * @param  array<int, string>  $strengths
     * @param  array<int, string>  $weaknesses
     * @param  array<int, string>  $missingConcepts
     * @param  array<int, string>  $evidence
     */
    public function __construct(
        public float $score,
        public float $confidence,
        public array $strengths,
        public array $weaknesses,
        public array $missingConcepts,
        public array $evidence,
        public bool $followUpRequired = false,
        public string $reasoningSummary = '',
    ) {}

    /**
     * The first concept the candidate failed to demonstrate, if any. Used to
     * aim a follow-up question at a specific gap.
     */
    public function primaryMissingConcept(): ?string
    {
        foreach ($this->missingConcepts as $concept) {
            $concept = trim($concept);

            if ($concept !== '') {
                return $concept;
            }
        }

        return null;
    }
}
