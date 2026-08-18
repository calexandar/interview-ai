<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $answer_id
 * @property float $score
 * @property float $technical_accuracy
 * @property float $depth
 * @property float $practical_experience
 * @property float $communication
 * @property float $confidence
 * @property array|null $strengths
 * @property array|null $weaknesses
 * @property array|null $missing_topics
 * @property bool $follow_up_required
 * @property string|null $reasoning_summary
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer_id',
        'score',
        'technical_accuracy',
        'depth',
        'practical_experience',
        'communication',
        'confidence',
        'strengths',
        'weaknesses',
        'missing_topics',
        'follow_up_required',
        'reasoning_summary',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:1',
            'technical_accuracy' => 'decimal:1',
            'depth' => 'decimal:1',
            'practical_experience' => 'decimal:1',
            'communication' => 'decimal:1',
            'confidence' => 'decimal:2',
            'strengths' => 'array',
            'weaknesses' => 'array',
            'missing_topics' => 'array',
            'follow_up_required' => 'boolean',
        ];
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}
