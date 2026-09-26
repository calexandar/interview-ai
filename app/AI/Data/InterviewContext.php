<?php

namespace App\AI\Data;

use App\Shared\Enums\QuestionDifficulty;

/**
 * Everything the AI is allowed to know when generating a question.
 *
 * Candidate-supplied text is present only as quoted data. Nothing here grants
 * authority, reveals evaluation criteria, or serializes an entire model.
 */
readonly class InterviewContext
{
    /**
     * @param  array<int, string>  $requiredSkills
     * @param  array<int, string>  $previousQuestions
     * @param  array<int, string>  $previousAnswers
     * @param  array<int, string>  $focusConcepts
     */
    public function __construct(
        public string $jobTitle,
        public string $jobDescription,
        public array $requiredSkills,
        public string $candidateName,
        public string $section,
        public array $previousQuestions,
        public array $previousAnswers,
        public ?string $currentSkill,
        public QuestionDifficulty $difficulty,
        public bool $isFollowUp = false,
        public array $focusConcepts = [],
    ) {}
}
