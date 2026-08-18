<?php

namespace Database\Factories;

use App\Models\Interview;
use App\Models\Skill;
use App\Models\SkillAssessment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkillAssessment>
 */
class SkillAssessmentFactory extends Factory
{
    protected $model = SkillAssessment::class;

    public function definition(): array
    {
        return [
            'interview_id' => Interview::factory(),
            'skill_id' => Skill::factory(),
            'score' => 0,
            'confidence' => 0,
            'questions_answered' => 0,
        ];
    }
}
