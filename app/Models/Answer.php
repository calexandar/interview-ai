<?php

namespace App\Models;

use Database\Factories\AnswerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $interview_question_id
 * @property int $candidate_id
 * @property string $content
 * @property int|null $duration_seconds
 * @property Carbon|null $submitted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Answer extends Model
{
    /** @use HasFactory<AnswerFactory> */
    use HasFactory;

    protected $fillable = ['interview_question_id', 'candidate_id', 'content', 'duration_seconds', 'submitted_at'];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<InterviewQuestion, $this>
     */
    public function interviewQuestion(): BelongsTo
    {
        return $this->belongsTo(InterviewQuestion::class);
    }

    /**
     * @return BelongsTo<Candidate, $this>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * @return HasOne<Evaluation, $this>
     */
    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }

    public function hasEvaluation(): bool
    {
        return $this->evaluation()->exists();
    }

    public function hasCompletedEvaluation(): bool
    {
        return $this->evaluation?->isCompleted() ?? false;
    }
}
