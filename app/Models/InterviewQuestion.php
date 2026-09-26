<?php

namespace App\Models;

use App\Shared\Enums\QuestionDifficulty;
use App\Shared\Enums\QuestionSource;
use App\Shared\Enums\QuestionStatus;
use Database\Factories\InterviewQuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $interview_id
 * @property int|null $question_id
 * @property Question|null $question
 * @property Skill|null $skill
 * @property int $position
 * @property int $skill_id
 * @property QuestionDifficulty $difficulty
 * @property string $question_text
 * @property QuestionStatus $status
 * @property QuestionSource $source
 * @property list<string>|null $evaluation_criteria
 * @property array<string, mixed>|null $generation_metadata
 * @property Carbon|null $asked_at
 * @property Carbon|null $answered_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InterviewQuestion extends Model
{
    /** @use HasFactory<InterviewQuestionFactory> */
    use HasFactory;

    protected $fillable = ['interview_id', 'question_id', 'position', 'skill_id', 'difficulty', 'question_text', 'status', 'source', 'evaluation_criteria', 'generation_metadata', 'asked_at', 'answered_at'];

    protected function casts(): array
    {
        return [
            'difficulty' => QuestionDifficulty::class,
            'status' => QuestionStatus::class,
            'source' => QuestionSource::class,
            'evaluation_criteria' => 'array',
            'generation_metadata' => 'array',
            'asked_at' => 'datetime',
            'answered_at' => 'datetime',
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
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * @return BelongsTo<Skill, $this>
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * @return HasOne<Answer, $this>
     */
    public function answer(): HasOne
    {
        return $this->hasOne(Answer::class);
    }

    public function isAnswered(): bool
    {
        return $this->status === QuestionStatus::Answered;
    }

    public function isAiGenerated(): bool
    {
        return $this->source->isAiGenerated();
    }

    /**
     * The criteria a question is judged against, whether it came from the
     * question bank or was generated. Never expose this to a candidate.
     *
     * @return array<int, string>
     */
    public function evaluationCriteria(): array
    {
        $bankCriteria = $this->question_id === null
            ? []
            : ($this->question->expected_topics ?? []);

        $criteria = $this->evaluation_criteria ?? $bankCriteria;

        return array_values(array_filter(array_map(
            static fn (mixed $criterion): string => trim((string) $criterion),
            $criteria,
        ), static fn (string $criterion): bool => $criterion !== ''));
    }

    /**
     * A candidate-safe description of evaluation state. Deliberately carries
     * no score, no confidence, no criteria and no reasoning, so the conduct
     * page can show progress without leaking the evaluator's material.
     */
    public function evaluationState(): string
    {
        $evaluation = $this->answer?->evaluation;

        return match (true) {
            $evaluation === null => 'pending',
            $evaluation->isFailed() => 'failed',
            $evaluation->isCompleted() => 'completed',
            default => 'pending',
        };
    }
}
