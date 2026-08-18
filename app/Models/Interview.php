<?php

namespace App\Models;

use App\Shared\Concerns\BelongsToOrganization;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\InterviewType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $organization_id
 * @property int $position_id
 * @property int $candidate_id
 * @property InterviewStatus $status
 * @property InterviewType $type
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property int|null $current_question_id
 * @property int $question_index
 * @property int $total_questions
 * @property array|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Interview extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'position_id',
        'candidate_id',
        'status',
        'type',
        'started_at',
        'completed_at',
        'current_question_id',
        'question_index',
        'total_questions',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => InterviewStatus::class,
            'type' => InterviewType::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function currentQuestion(): BelongsTo
    {
        return $this->belongsTo(InterviewQuestion::class, 'current_question_id');
    }

    public function interviewQuestions(): HasMany
    {
        return $this->hasMany(InterviewQuestion::class);
    }

    public function skillAssessments(): HasMany
    {
        return $this->hasMany(SkillAssessment::class);
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    public function isScheduled(): bool
    {
        return $this->status === InterviewStatus::Scheduled;
    }

    public function isInProgress(): bool
    {
        return $this->status === InterviewStatus::InProgress;
    }

    public function isCompleted(): bool
    {
        return $this->status === InterviewStatus::Completed;
    }

    public function hasReachedQuestionLimit(): bool
    {
        return $this->question_index >= $this->total_questions;
    }
}
