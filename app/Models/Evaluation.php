<?php

namespace App\Models;

use App\Shared\Enums\EvaluationStatus;
use Database\Factories\EvaluationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $answer_id
 * @property float $score
 * @property float|null $technical_accuracy
 * @property float|null $depth
 * @property float|null $practical_experience
 * @property float|null $communication
 * @property float $confidence
 * @property list<string>|null $strengths
 * @property list<string>|null $weaknesses
 * @property list<string>|null $missing_topics
 * @property array<string, mixed>|null $evidence
 * @property bool $follow_up_required
 * @property string|null $reasoning_summary
 * @property EvaluationStatus $status
 * @property int $attempts
 * @property string|null $failure_reason
 * @property Carbon|null $failed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Evaluation extends Model
{
    /** @use HasFactory<EvaluationFactory> */
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
        'evidence',
        'follow_up_required',
        'reasoning_summary',
        'status',
        'attempts',
        'failure_reason',
        'failed_at',
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
            'evidence' => 'array',
            'follow_up_required' => 'boolean',
            'status' => EvaluationStatus::class,
            'attempts' => 'integer',
            'failed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Answer, $this>
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }

    /**
     * @param  Builder<Evaluation>  $query
     * @return Builder<Evaluation>
     */
    public function scopeSettled(Builder $query): Builder
    {
        return $query->where('status', EvaluationStatus::Completed);
    }

    /**
     * @param  Builder<Evaluation>  $query
     * @return Builder<Evaluation>
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', EvaluationStatus::Failed);
    }

    public function isCompleted(): bool
    {
        return $this->status === EvaluationStatus::Completed;
    }

    public function isFailed(): bool
    {
        return $this->status === EvaluationStatus::Failed;
    }

    /**
     * Whether this evaluation still needs work, either because it was never
     * attempted or because the last attempt failed. Used to decide if a retry
     * is allowed, never to decide if a score exists.
     */
    public function needsEvaluation(): bool
    {
        return ! $this->isCompleted();
    }
}
