<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Candidate;
use App\Models\InterviewQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition(): array
    {
        return [
            'interview_question_id' => InterviewQuestion::factory(),
            'candidate_id' => Candidate::factory(),
            'content' => fake()->paragraph(),
            'submitted_at' => now(),
        ];
    }
}
