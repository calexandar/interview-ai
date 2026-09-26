<?php

namespace App\AI\Exceptions;

use RuntimeException;
use Throwable;

/**
 * The single failure type the Interview domain understands.
 *
 * Provider timeouts, rate limits, outages, malformed responses and output that
 * fails validation all arrive as this exception so the interview lifecycle has
 * exactly one failure path to handle, and no provider detail leaks upward.
 */
class AIUnavailableException extends RuntimeException
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public readonly string $category,
        string $message,
        public readonly array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public static function timeout(int $seconds, ?Throwable $previous = null): self
    {
        return new self(
            'timeout',
            "The AI provider did not respond within {$seconds} seconds.",
            ['timeout' => $seconds],
            $previous,
        );
    }

    public static function rateLimited(?Throwable $previous = null): self
    {
        return new self('rate_limited', 'The AI provider rate limited this request.', [], $previous);
    }

    public static function unavailable(?Throwable $previous = null): self
    {
        return new self('unavailable', 'The AI provider is unavailable.', [], $previous);
    }

    public static function overloaded(?Throwable $previous = null): self
    {
        return new self('overloaded', 'The AI provider is overloaded.', [], $previous);
    }

    public static function malformedResponse(string $detail, ?Throwable $previous = null): self
    {
        return new self('malformed', "The AI provider returned an unusable response: {$detail}", [], $previous);
    }

    public static function invalidOutput(string $detail, ?Throwable $previous = null): self
    {
        return new self('invalid_output', "The AI response failed validation: {$detail}", [], $previous);
    }
}
