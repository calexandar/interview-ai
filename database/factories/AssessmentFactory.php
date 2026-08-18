<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Interview;
use App\Shared\Enums\AssessmentRecommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'interview_id' => Interview::factory(),
            'overall_score' => fake()->randomFloat(1, 4, 10),
            'recommendation' => fake()->randomElement(AssessmentRecommendation::cases()),
            'confidence' => fake()->randomFloat(2, 0.5, 1.0),
            'strengths' => null,
            'weaknesses' => null,
            'skill_summary' => null,
            'summary' => fake()->paragraph(),
        ];
    }

    public function strongHire(): static
    {
        return $this->state(fn () => [
            'overall_score' => 8.5,
            'recommendation' => AssessmentRecommendation::StrongHire,
            'confidence' => 0.92,
            'strengths' => ['Strong Laravel knowledge', 'Good architecture skills'],
        ]);
    }
}
