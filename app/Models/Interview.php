<?php

namespace App\Models;

use App\Shared\Concerns\BelongsToOrganization;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\InterviewType;
use App\Shared\Enums\QuestionSource;
use App\Shared\Enums\QuestionStatus;
use Carbon\CarbonImmutable;
use Database\Factories\InterviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $organization_id
 * @property int $position_id
 * @property Position|null $position
 * @property int $candidate_id
 * @property InterviewStatus $status
 * @property InterviewType $type
 * @property CarbonImmutable|null $started_at
 * @property CarbonImmutable|null $completed_at
 * @property CarbonImmutable|null $paused_at
 * @property int|null $current_question_id
 * @property int $question_index
 * @property int $total_questions
 * @property array<string, mixed>|null $metadata
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
class Interview extends Model
{
    /** @use HasFactory<InterviewFactory> */
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

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return BelongsTo<Candidate, $this>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * @return BelongsTo<InterviewQuestion, $this>
     */
    public function currentQuestion(): BelongsTo
    {
        return $this->belongsTo(InterviewQuestion::class, 'current_question_id');
    }

    /**
     * @return HasMany<InterviewQuestion, $this>
     */
    public function interviewQuestions(): HasMany
    {
        return $this->hasMany(InterviewQuestion::class);
    }

    /**
     * @return HasMany<SkillAssessment, $this>
     */
    public function skillAssessments(): HasMany
    {
        return $this->hasMany(SkillAssessment::class);
    }

    /**
     * @return HasOne<Assessment, $this>
     */
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
        // position_id is a non-nullable foreign key, so the position always
        // resolves and duration_minutes always has a value.
        return $this->position->duration_minutes * 60;
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

    /**
     * The next unused question-bank question for a given skill, used as the
     * fallback when the AI cannot produce a question. Bank questions that
     * were never asked stay Pending precisely so they are available here.
     */
    public function nextPendingBankQuestionForSkill(int $skillId): ?InterviewQuestion
    {
        return $this->interviewQuestions()
            ->where('status', QuestionStatus::Pending)
            ->where('source', QuestionSource::QuestionBank)
            ->where('skill_id', $skillId)
            ->orderBy('position')
            ->first();
    }

    public function nextPendingBankQuestion(): ?InterviewQuestion
    {
        return $this->interviewQuestions()
            ->where('status', QuestionStatus::Pending)
            ->where('source', QuestionSource::QuestionBank)
            ->orderBy('position')
            ->first();
    }

    /**
     * How many questions have already been asked for a skill, including the
     * one currently being asked. Drives skill rotation and difficulty drift.
     */
    public function askedCountForSkill(int $skillId): int
    {
        return $this->interviewQuestions()
            ->where('skill_id', $skillId)
            ->whereIn('status', [
                QuestionStatus::Asking,
                QuestionStatus::Answering,
                QuestionStatus::Processing,
                QuestionStatus::Answered,
            ])
            ->count();
    }

    public function nextQuestionPosition(): int
    {
        return ((int) $this->interviewQuestions()->max('position')) + 1;
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
