<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Position;
use App\Shared\Enums\PositionLevel;
use App\Shared\Enums\PositionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'level' => fake()->randomElement(PositionLevel::cases()),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'question_count' => fake()->numberBetween(5, 15),
            'status' => PositionStatus::Active,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => PositionStatus::Inactive]);
    }
}
