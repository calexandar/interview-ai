<?php

namespace App\Listeners;

use App\AI\Exceptions\AIUnavailableException;
use Illuminate\Support\Str;
use Laravel\Ai\Events\AgentFailed;
use Laravel\Ai\Events\AgentPrompted;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Observability for AI calls: which provider and model ran, how many tokens it
 * cost, how long it took, and how it ended.
 *
 * Prompt bodies, candidate answers, evaluation criteria and API secrets are
 * never written to the log. Only non-sensitive metadata is recorded, so an
 * incident review can see cost and reliability without exposing interview
 * content or personal data.
 */
class LogAiInvocation
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function handlePrompted(AgentPrompted $event): void
    {
        $response = $event->response;

        $usage = $response->usage;
        $meta = $response->meta;

        $this->logger->info('ai.invocation', [
            'event' => 'prompted',
            'invocation_id' => $response->invocationId,
            'agent' => $this->agentName($event->prompt),
            'provider' => $meta->provider,
            'model' => $meta->model,
            'prompt_tokens' => $usage->promptTokens,
            'completion_tokens' => $usage->completionTokens,
            'reasoning_tokens' => $usage->reasoningTokens,
            'total_tokens' => $usage->promptTokens + $usage->completionTokens,
        ]);
    }

    public function handleFailed(AgentFailed $event): void
    {
        $this->logger->warning('ai.invocation', [
            'event' => 'failed',
            'invocation_id' => $event->invocationId,
            'agent' => $this->agentName($event->prompt),
            'error_category' => $this->categorize($event->exception),
            'error_type' => $event->exception::class,
        ]);
    }

    private function agentName(object $prompt): ?string
    {
        $agent = $prompt->agent ?? null;

        return $agent === null ? null : $agent::class;
    }

    private function categorize(Throwable $e): string
    {
        if ($e instanceof AIUnavailableException) {
            return $e->category;
        }

        $message = Str::lower($e->getMessage());

        return match (true) {
            Str::contains($message, ['rate limit', 'too many requests']) => 'rate_limited',
            Str::contains($message, ['timed out', 'timeout', 'deadline exceeded']) => 'timeout',
            Str::contains($message, ['overloaded', 'capacity']) => 'overloaded',
            default => 'unavailable',
        };
    }
}
