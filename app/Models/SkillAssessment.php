<?php

namespace App\Models;

use Database\Factories\SkillAssessmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $interview_id
 * @property int $skill_id
 * @property float $score
 * @property float $confidence
 * @property int $questions_answered
 * @property list<string>|null $evidence
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SkillAssessment extends Model
{
    /** @use HasFactory<SkillAssessmentFactory> */
    use HasFactory;

    protected $fillable = ['interview_id', 'skill_id', 'score', 'confidence', 'questions_answered', 'evidence'];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:1',
            'confidence' => 'decimal:2',
            'evidence' => 'array',
            'questions_answered' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Interview, $this>
     */
    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    /**
     * @return BelongsTo<Skill, $this>
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
