<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Organization;
use App\Models\Position;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\InterviewType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interview>
 */
class InterviewFactory extends Factory
{
    protected $model = Interview::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'position_id' => Position::factory(),
            'candidate_id' => Candidate::factory(),
            'status' => InterviewStatus::Scheduled,
            'type' => InterviewType::Technical,
            'total_questions' => 10,
            'question_index' => 0,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => InterviewStatus::InProgress,
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => InterviewStatus::Completed,
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);
    }
}
