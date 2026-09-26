<?php

namespace App\AI;

use App\AI\Contracts\InterviewAI;
use App\AI\Data\AnswerEvaluation;
use App\AI\Data\EvaluationContext;
use App\AI\Data\GeneratedQuestion;
use App\AI\Data\InterviewContext;
use App\AI\Exceptions\AIUnavailableException;
use App\AI\Prompts\EvaluateAnswer;
use App\AI\Prompts\GenerateQuestion;
use App\Shared\Enums\QuestionDifficulty;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Ai\Exceptions\AiException;
use Laravel\Ai\Exceptions\ProviderConnectionException;
use Laravel\Ai\Exceptions\ProviderOverloadedException;
use Laravel\Ai\Exceptions\RateLimitedException;
use Throwable;

/**
 * The production implementation of the interview AI contract, backed by the
 * Laravel AI SDK.
 *
 * Two responsibilities beyond calling the agents: map every provider failure
 * onto a single exception type, and re-validate the structured payload before
 * handing a DTO back. A model is never trusted just because it produced
 * well-formed JSON.
 */
class InterviewAIProvider implements InterviewAI
{
    /**
     * Non-sensitive metadata about the most recent successful call.
     *
     * @var array<string, int|string|null>
     */
    private array $lastInvocation = [];

    public function generateQuestion(InterviewContext $context): GeneratedQuestion
    {
        $response = $this->dispatch(function () use ($context): mixed {
            $agent = app(GenerateQuestion::class);

            return $agent->prompt($agent->buildRequest($context), timeout: $this->timeout());
        });

        $this->lastInvocation = $this->invocationMetadata($response);

        return $this->toGeneratedQuestion($this->payload($response, 'question'));
    }

    public function evaluateAnswer(EvaluationContext $context): AnswerEvaluation
    {
        $response = $this->dispatch(function () use ($context): mixed {
            $agent = app(EvaluateAnswer::class);

            return $agent->prompt($agent->buildRequest($context), timeout: $this->timeout());
        });

        $this->lastInvocation = $this->invocationMetadata($response);

        return $this->toAnswerEvaluation($this->payload($response, 'evaluation'));
    }

    public function lastInvocation(): array
    {
        return $this->lastInvocation;
    }

    /**
     * Runs the agent call and normalizes every failure mode. Nothing below this
     * method can leak a provider exception to the Interview domain.
     */
    private function dispatch(callable $call): mixed
    {
        try {
            return $call();
        } catch (AIUnavailableException $e) {
            throw $e;
        } catch (RateLimitedException $e) {
            throw AIUnavailableException::rateLimited($e);
        } catch (ProviderOverloadedException $e) {
            throw AIUnavailableException::overloaded($e);
        } catch (ProviderConnectionException $e) {
            throw AIUnavailableException::unavailable($e);
        } catch (AiException $e) {
            throw AIUnavailableException::unavailable($e);
        } catch (Throwable $e) {
            if ($this->isTimeout($e)) {
                throw AIUnavailableException::timeout((int) config('interview.provider.timeout', 60), $e);
            }

            throw AIUnavailableException::unavailable($e);
        }
    }

    private function isTimeout(Throwable $e): bool
    {
        $message = Str::lower($e->getMessage());

        return Str::contains($message, ['timed out', 'timeout', 'deadline exceeded']);
    }

    private function timeout(): int
    {
        return (int) config('interview.provider.timeout', 60);
    }

    /**
     * Extracts the structured payload from a response, rejecting anything that
     * is not a non-empty array.
     *
     * @return array<string, mixed>
     */
    private function payload(mixed $response, string $operation): array
    {
        $structured = is_object($response) && method_exists($response, 'toArray')
            ? $response->toArray()
            : $response;

        if (! is_array($structured) || $structured === []) {
            throw AIUnavailableException::malformedResponse("{$operation} returned no structured data");
        }

        /** @var array<string, mixed> $structured */
        return $structured;
    }

    /**
     * @return array<string, int|string|null>
     */
    private function invocationMetadata(mixed $response): array
    {
        if (! is_object($response)) {
            return [];
        }

        $usage = property_exists($response, 'usage') ? $response->usage : null;
        $meta = property_exists($response, 'meta') ? $response->meta : null;

        $totalTokens = null;
        if ($usage !== null) {
            $totalTokens = (int) $usage->promptTokens + (int) $usage->completionTokens;
        }

        return [
            'provider' => $meta?->provider,
            'model' => $meta?->model,
            'prompt_tokens' => $usage?->promptTokens,
            'completion_tokens' => $usage?->completionTokens,
            'total_tokens' => $totalTokens,
            'invocation_id' => property_exists($response, 'invocationId') ? $response->invocationId : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function toGeneratedQuestion(array $data): GeneratedQuestion
    {
        $question = $this->requiredString($data, 'question', 'question');
        $maxChars = (int) config('interview.output.max_question_chars', 1000);

        if (Str::length($question) > $maxChars) {
            throw AIUnavailableException::invalidOutput("question exceeds {$maxChars} characters");
        }

        $skill = Arr::get($data, 'skill');
        $skill = is_string($skill) ? trim($skill) : null;

        $difficulty = $this->toDifficulty(Arr::get($data, 'difficulty'));

        $criteria = $this->toStringList(
            $data['evaluation_criteria'] ?? [],
            'evaluation_criteria',
            (int) config('interview.output.max_criteria', 8),
        );

        if (count($criteria) < (int) config('interview.output.min_criteria', 1)) {
            throw AIUnavailableException::invalidOutput('evaluation_criteria is empty');
        }

        return new GeneratedQuestion(
            question: $question,
            skill: ($skill === null || $skill === '') ? null : $skill,
            difficulty: $difficulty,
            evaluationCriteria: $criteria,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function toAnswerEvaluation(array $data): AnswerEvaluation
    {
        $maxScore = (float) config('interview.output.max_score', 10);
        $minScore = (float) config('interview.output.min_score', 0);

        $score = $this->requiredFloat($data, 'score', 'evaluation');
        if ($score < $minScore || $score > $maxScore) {
            throw AIUnavailableException::invalidOutput("score {$score} is outside {$minScore}-{$maxScore}");
        }

        $confidence = $this->requiredFloat($data, 'confidence', 'evaluation');
        if ($confidence < 0 || $confidence > 1) {
            throw AIUnavailableException::invalidOutput("confidence {$confidence} is outside 0-1");
        }

        return new AnswerEvaluation(
            score: round($score, 1),
            confidence: round($confidence, 2),
            strengths: $this->toStringList($data['strengths'] ?? [], 'strengths', 12),
            weaknesses: $this->toStringList($data['weaknesses'] ?? [], 'weaknesses', 12),
            missingConcepts: $this->toStringList($data['missing_concepts'] ?? [], 'missing_concepts', 12),
            evidence: $this->toStringList($data['evidence'] ?? [], 'evidence', 12),
            followUpRequired: (bool) ($data['follow_up_required'] ?? false),
            reasoningSummary: is_string($data['reasoning_summary'] ?? null)
                ? Str::limit($data['reasoning_summary'], 2000)
                : '',
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function requiredString(array $data, string $key, string $operation): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || trim($value) === '') {
            throw AIUnavailableException::invalidOutput("{$operation} is missing a usable \"{$key}\"");
        }

        return trim($value);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function requiredFloat(array $data, string $key, string $operation): float
    {
        $value = $data[$key] ?? null;

        if (is_bool($value) || ! is_numeric($value)) {
            throw AIUnavailableException::invalidOutput("{$operation} is missing a numeric \"{$key}\"");
        }

        return (float) $value;
    }

    private function toDifficulty(mixed $value): QuestionDifficulty
    {
        if ($value instanceof QuestionDifficulty) {
            return $value;
        }

        if (is_string($value)) {
            $difficulty = QuestionDifficulty::tryFrom(strtolower(trim($value)));

            if ($difficulty !== null) {
                return $difficulty;
            }
        }

        throw AIUnavailableException::invalidOutput('difficulty is not a recognised level');
    }

    /**
     * Normalizes a JSON array into a clean list of non-empty strings, dropping
     * anything the model improvised that is not a usable string.
     *
     * @return array<int, string>
     */
    private function toStringList(mixed $value, string $field, int $max): array
    {
        if (! is_array($value)) {
            return [];
        }

        $items = [];

        foreach ($value as $item) {
            if (is_string($item) && trim($item) !== '') {
                $items[] = trim($item);
            }
        }

        return array_slice($items, 0, $max);
    }
}
