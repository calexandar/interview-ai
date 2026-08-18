<?php

namespace App\Models;

use App\Shared\Enums\AssessmentRecommendation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $interview_id
 * @property float $overall_score
 * @property AssessmentRecommendation $recommendation
 * @property float $confidence
 * @property array|null $strengths
 * @property array|null $weaknesses
 * @property array|null $skill_summary
 * @property string|null $summary
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_id',
        'overall_score',
        'recommendation',
        'confidence',
        'strengths',
        'weaknesses',
        'skill_summary',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'overall_score' => 'decimal:1',
            'recommendation' => AssessmentRecommendation::class,
            'confidence' => 'decimal:2',
            'strengths' => 'array',
            'weaknesses' => 'array',
            'skill_summary' => 'array',
        ];
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }
}
