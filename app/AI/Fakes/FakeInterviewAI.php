<?php

namespace App\AI\Fakes;

use App\AI\Contracts\InterviewAI;
use App\AI\Data\AnswerEvaluation;
use App\AI\Data\EvaluationContext;
use App\AI\Data\GeneratedQuestion;
use App\AI\Data\InterviewContext;
use App\AI\Exceptions\AIUnavailableException;
use App\Shared\Enums\QuestionDifficulty;
use Closure;
use RuntimeException;

/**
 * A controllable stand-in for the AI, so tests exercise application behaviour
 * without ever reaching a provider.
 *
 * Tests assert on what the application handed the AI (lastInterviewContext) as
 * well as what it did with the answer, never on provider-specific behaviour.
 */
class FakeInterviewAI implements InterviewAI
{
    /** @var array<int, GeneratedQuestion> */
    private array $questions = [];

    /** @var array<int, AnswerEvaluation> */
    private array $evaluations = [];

    private ?AIUnavailableException $generateFailure = null;

    private ?AIUnavailableException $evaluateFailure = null;

    private ?RuntimeException $generateException = null;

    private ?RuntimeException $evaluateException = null;

    private ?InterviewContext $lastInterviewContext = null;

    private ?EvaluationContext $lastEvaluationContext = null;

    private int $generateCalls = 0;

    private int $evaluateCalls = 0;

    /**
     * Queue a question to be returned. Without this, a deterministic default
     * question is generated so a test only has to configure what it cares about.
     *
     * @param  array<int, string>  $evaluationCriteria
     */
    public function nextQuestion(
        string $question = 'How would you diagnose a slow Laravel application in production?',
        ?string $skill = null,
        string $difficulty = 'medium',
        array $evaluationCriteria = ['profiling', 'query analysis'],
    ): self {
        $this->questions[] = new GeneratedQuestion(
            question: $question,
            skill: $skill,
            difficulty: QuestionDifficulty::from($difficulty),
            evaluationCriteria: $evaluationCriteria,
        );

        return $this;
    }

    public function nextGeneratedQuestion(GeneratedQuestion $question): self
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * @param  array<int, string>  $strengths
     * @param  array<int, string>  $weaknesses
     * @param  array<int, string>  $missingConcepts
     * @param  array<int, string>  $evidence
     */
    public function evaluation(
        float $score = 7.5,
        float $confidence = 0.85,
        array $strengths = ['Understands eager loading'],
        array $weaknesses = ['Did not mention database indexes'],
        array $missingConcepts = ['indexing'],
        array $evidence = ['Candidate described eager loading as a solution to N+1 queries.'],
        bool $followUpRequired = true,
        string $reasoningSummary = 'Solid practical answer with a clear gap around indexing.',
    ): self {
        $this->evaluations[] = new AnswerEvaluation(
            score: $score,
            confidence: $confidence,
            strengths: $strengths,
            weaknesses: $weaknesses,
            missingConcepts: $missingConcepts,
            evidence: $evidence,
            followUpRequired: $followUpRequired,
            reasoningSummary: $reasoningSummary,
        );

        return $this;
    }

    public function nextEvaluation(AnswerEvaluation $evaluation): self
    {
        $this->evaluations[] = $evaluation;

        return $this;
    }

    /**
     * Fail the next question generation the way a real provider outage would.
     */
    public function throwOnGenerate(
        string $category = 'unavailable',
        string $message = 'The AI provider is unavailable.',
        ?RuntimeException $previous = null,
    ): self {
        $this->generateFailure = new AIUnavailableException($category, $message, [], $previous);

        return $this;
    }

    /**
     * Fail the next answer evaluation the way a real provider outage would.
     */
    public function throwOnEvaluate(
        string $category = 'unavailable',
        string $message = 'The AI provider is unavailable.',
        ?RuntimeException $previous = null,
    ): self {
        $this->evaluateFailure = new AIUnavailableException($category, $message, [], $previous);

        return $this;
    }

    /**
     * Make the provider blow up with something that is not an AI exception, so
     * a test can prove the lifecycle still contains the blast radius.
     */
    public function throwRawOnEvaluate(RuntimeException $exception): self
    {
        $this->evaluateException = $exception;

        return $this;
    }

    /**
     * The generation-path equivalent of throwRawOnEvaluate().
     */
    public function throwRawOnGenerate(RuntimeException $exception): self
    {
        $this->generateException = $exception;

        return $this;
    }

    public function generateQuestion(InterviewContext $context): GeneratedQuestion
    {
        $this->generateCalls++;
        $this->lastInterviewContext = $context;

        if ($this->generateException instanceof RuntimeException) {
            $exception = $this->generateException;
            $this->generateException = null;

            throw $exception;
        }

        if ($this->generateFailure instanceof AIUnavailableException) {
            $failure = $this->generateFailure;
            $this->generateFailure = null;

            throw $failure;
        }

        $question = array_shift($this->questions);

        if ($question instanceof GeneratedQuestion) {
            return $question;
        }

        return new GeneratedQuestion(
            question: 'Describe how you would approach this problem in a real codebase.',
            skill: $context->currentSkill,
            difficulty: $context->difficulty,
            evaluationCriteria: ['problem framing', 'trade-off analysis'],
        );
    }

    public function evaluateAnswer(EvaluationContext $context): AnswerEvaluation
    {
        $this->evaluateCalls++;
        $this->lastEvaluationContext = $context;

        if ($this->evaluateException instanceof RuntimeException) {
            $exception = $this->evaluateException;
            $this->evaluateException = null;

            throw $exception;
        }

        if ($this->evaluateFailure instanceof AIUnavailableException) {
            $failure = $this->evaluateFailure;
            $this->evaluateFailure = null;

            throw $failure;
        }

        $evaluation = array_shift($this->evaluations);

        if ($evaluation instanceof AnswerEvaluation) {
            return $evaluation;
        }

        return new AnswerEvaluation(
            score: 7.5,
            confidence: 0.85,
            strengths: ['Understands eager loading'],
            weaknesses: ['Did not mention database indexes'],
            missingConcepts: ['indexing'],
            evidence: ['Candidate described eager loading as a solution to N+1 queries.'],
            followUpRequired: true,
            reasoningSummary: 'Solid practical answer with a clear gap around indexing.',
        );
    }

    public function lastInvocation(): array
    {
        return [
            'provider' => 'fake',
            'model' => 'fake-model',
            'total_tokens' => 0,
        ];
    }

    public function lastInterviewContext(): ?InterviewContext
    {
        return $this->lastInterviewContext;
    }

    public function lastEvaluationContext(): ?EvaluationContext
    {
        return $this->lastEvaluationContext;
    }

    public function generateQuestionCount(): int
    {
        return $this->generateCalls;
    }

    public function evaluateAnswerCount(): int
    {
        return $this->evaluateCalls;
    }

    public function assertReceivedContext(Closure $callback): self
    {
        if ($this->lastInterviewContext === null) {
            throw new RuntimeException('No interview context was received.');
        }

        if ($callback($this->lastInterviewContext) !== true) {
            throw new RuntimeException('The received interview context did not match the expectation.');
        }

        return $this;
    }

    public function assertReceivedEvaluationContext(Closure $callback): self
    {
        if ($this->lastEvaluationContext === null) {
            throw new RuntimeException('No evaluation context was received.');
        }

        if ($callback($this->lastEvaluationContext) !== true) {
            throw new RuntimeException('The received evaluation context did not match the expectation.');
        }

        return $this;
    }
}
