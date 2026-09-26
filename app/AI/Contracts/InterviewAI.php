<?php

namespace App\AI\Contracts;

use App\AI\Data\AnswerEvaluation;
use App\AI\Data\EvaluationContext;
use App\AI\Data\GeneratedQuestion;
use App\AI\Data\InterviewContext;
use App\AI\Exceptions\AIUnavailableException;

/**
 * The Interview domain's only view of the AI.
 *
 * Implementations must be provider-independent from the caller's point of
 * view: they return validated DTOs or throw AIUnavailableException. Callers
 * never see a provider payload, a raw response, or a provider SDK type.
 */
interface InterviewAI
{
    /**
     * @throws AIUnavailableException
     */
    public function generateQuestion(InterviewContext $context): GeneratedQuestion;

    /**
     * @throws AIUnavailableException
     */
    public function evaluateAnswer(EvaluationContext $context): AnswerEvaluation;

    /**
     * Non-sensitive metadata about the most recent call, for observability and
     * for storing generation metadata alongside a generated question.
     *
     * @return array<string, int|string|null>
     */
    public function lastInvocation(): array;
}
