<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Skill;
use App\Shared\Enums\QuestionDifficulty;
use App\Shared\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'skill_id' => Skill::factory(),
            'type' => fake()->randomElement(QuestionType::cases()),
            'difficulty' => fake()->randomElement(QuestionDifficulty::cases()),
            'question' => fake()->sentence(),
            'expected_topics' => null,
            'evaluation_guidance' => null,
            'is_active' => true,
        ];
    }

    public function forSkill(Skill $skill): static
    {
        return $this->state(fn () => ['skill_id' => $skill->id]);
    }

    public function difficulty(QuestionDifficulty $difficulty): static
    {
        return $this->state(fn () => ['difficulty' => $difficulty]);
    }
}
