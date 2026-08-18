<?php

namespace App\Models;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SkillAssessment extends Model
{
    use HasFactory;

    protected $fillable = ['interview_id', 'skill_id', 'score', 'confidence', 'questions_answered'];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:1',
            'confidence' => 'decimal:2',
        ];
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
