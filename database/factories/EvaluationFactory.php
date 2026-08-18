<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Evaluation;
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
            'follow_up_required' => false,
            'reasoning_summary' => fake()->sentence(),
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
}
