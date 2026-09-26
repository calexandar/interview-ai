<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Evaluation;
use App\Shared\Enums\EvaluationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluation>
 */
class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition(): array
    {
        return [
            'answer_id' => Answer::factory(),
            'score' => fake()->randomFloat(1, 3, 10),
            'technical_accuracy' => fake()->randomFloat(1, 3, 10),
            'depth' => fake()->randomFloat(1, 3, 10),
            'practical_experience' => fake()->randomFloat(1, 3, 10),
            'communication' => fake()->randomFloat(1, 3, 10),
            'confidence' => fake()->randomFloat(2, 0.5, 1.0),
            'strengths' => null,
            'weaknesses' => null,
            'missing_topics' => null,
            'evidence' => null,
            'follow_up_required' => false,
            'reasoning_summary' => fake()->sentence(),
            'status' => EvaluationStatus::Completed,
            'attempts' => 1,
            'failure_reason' => null,
            'failed_at' => null,
        ];
    }

    public function strong(): static
    {
        return $this->state(fn () => [
            'score' => 8.5,
            'technical_accuracy' => 9.0,
            'depth' => 8.0,
            'practical_experience' => 8.5,
            'communication' => 8.0,
            'confidence' => 0.9,
            'strengths' => ['Strong understanding', 'Good examples'],
            'follow_up_required' => false,
        ]);
    }

    public function weak(): static
    {
        return $this->state(fn () => [
            'score' => 4.0,
            'technical_accuracy' => 3.5,
            'depth' => 3.0,
            'practical_experience' => 4.0,
            'communication' => 5.0,
            'confidence' => 0.4,
            'weaknesses' => ['Missing key concepts'],
            'missing_topics' => ['advanced topics'],
            'follow_up_required' => true,
        ]);
    }

    /**
     * An evaluation produced by the AI. The AI returns a single overall score
     * with evidence, not four derived sub-dimension scores, so those columns
     * are deliberately left null rather than invented.
     */
    public function aiEvaluated(): static
    {
        return $this->state(fn () => [
            'technical_accuracy' => null,
            'depth' => null,
            'practical_experience' => null,
            'communication' => null,
            'strengths' => ['Understands eager loading'],
            'weaknesses' => ['Did not mention database indexes'],
            'missing_topics' => ['indexing'],
            'evidence' => ['Candidate described eager loading as a solution to N+1 queries.'],
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => EvaluationStatus::Pending,
            'attempts' => 0,
            'failure_reason' => null,
            'failed_at' => null,
        ]);
    }

    public function failed(string $reason = 'provider_unavailable'): static
    {
        return $this->state(fn () => [
            'status' => EvaluationStatus::Failed,
            'failure_reason' => $reason,
            'failed_at' => now(),
        ]);
    }
}
