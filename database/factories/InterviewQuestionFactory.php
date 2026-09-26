<?php

namespace Database\Factories;

use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Question;
use App\Models\Skill;
use App\Shared\Enums\QuestionDifficulty;
use App\Shared\Enums\QuestionSource;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InterviewQuestion>
 */
class InterviewQuestionFactory extends Factory
{
    protected $model = InterviewQuestion::class;

    public function definition(): array
    {
        return [
            'interview_id' => Interview::factory(),
            'question_id' => Question::factory(),
            'position' => 1,
            'skill_id' => Skill::factory(),
            'difficulty' => QuestionDifficulty::Medium,
            'question_text' => fake()->sentence(),
            'status' => QuestionStatus::Pending,
            'source' => QuestionSource::QuestionBank,
            'evaluation_criteria' => null,
            'generation_metadata' => null,
        ];
    }

    public function aiGenerated(): static
    {
        return $this->state(fn () => [
            'question_id' => null,
            'source' => QuestionSource::AI,
            'evaluation_criteria' => ['profiling', 'caching'],
            'generation_metadata' => [
                'provider' => 'fake',
                'model' => 'fake-model',
                'latency_ms' => 12,
                'total_tokens' => 320,
            ],
        ]);
    }

    public function asking(): static
    {
        return $this->state(fn () => [
            'status' => QuestionStatus::Asking,
            'asked_at' => now(),
        ]);
    }

    public function answered(): static
    {
        return $this->state(fn () => [
            'status' => QuestionStatus::Answered,
            'asked_at' => now()->subMinutes(5),
            'answered_at' => now(),
        ]);
    }

    public function skipped(): static
    {
        return $this->state(fn () => [
            'status' => QuestionStatus::Skipped,
            'asked_at' => now()->subMinutes(2),
        ]);
    }
}
