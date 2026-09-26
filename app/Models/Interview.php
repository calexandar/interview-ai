<?php

namespace App\Models;

use App\Shared\Concerns\BelongsToOrganization;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\InterviewType;
use App\Shared\Enums\QuestionStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $organization_id
 * @property int $position_id
 * @property int $candidate_id
 * @property InterviewStatus $status
 * @property InterviewType $type
 * @property CarbonImmutable|null $started_at
 * @property CarbonImmutable|null $completed_at
 * @property CarbonImmutable|null $paused_at
 * @property int|null $current_question_id
 * @property int $question_index
 * @property int $total_questions
 * @property array|null $metadata
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
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
        'paused_at',
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
            'paused_at' => 'datetime',
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

    public function isPaused(): bool
    {
        return $this->status === InterviewStatus::Paused;
    }

    public function isCompleted(): bool
    {
        return $this->status === InterviewStatus::Completed;
    }

    public function isExpired(): bool
    {
        return $this->status === InterviewStatus::Expired;
    }

    public function canBeStarted(): bool
    {
        return $this->status->canStart();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function hasReachedQuestionLimit(): bool
    {
        return $this->question_index >= $this->total_questions;
    }

    public function durationSeconds(): int
    {
        return ($this->position?->duration_minutes ?? 0) * 60;
    }

    public function remainingSeconds(): int
    {
        if ($this->started_at === null) {
            return $this->durationSeconds();
        }

        $elapsed = (int) $this->started_at->diffInSeconds(now());

        return max(0, $this->durationSeconds() - $elapsed);
    }

    public function hasExpired(): bool
    {
        if ($this->started_at === null || ! $this->isActive()) {
            return false;
        }

        return $this->remainingSeconds() <= 0;
    }

    public function activeQuestion(): ?InterviewQuestion
    {
        if ($this->current_question_id === null) {
            return null;
        }

        return $this->interviewQuestions()
            ->where('id', $this->current_question_id)
            ->first();
    }

    public function nextUnansweredQuestion(): ?InterviewQuestion
    {
        return $this->interviewQuestions()
            ->where('status', QuestionStatus::Pending)
            ->orderBy('position')
            ->first();
    }

    public function answeredCount(): int
    {
        return $this->interviewQuestions()
            ->where('status', QuestionStatus::Answered)
            ->count();
    }

    public function progressPercentage(): int
    {
        if ($this->total_questions === 0) {
            return 0;
        }

        return (int) floor(($this->question_index / $this->total_questions) * 100);
    }
}
