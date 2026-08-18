<?php

namespace App\Models;

use App\Shared\Enums\QuestionDifficulty;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $interview_id
 * @property int|null $question_id
 * @property int $position
 * @property int $skill_id
 * @property QuestionDifficulty $difficulty
 * @property string $question_text
 * @property QuestionStatus $status
 * @property Carbon|null $asked_at
 * @property Carbon|null $answered_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InterviewQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['interview_id', 'question_id', 'position', 'skill_id', 'difficulty', 'question_text', 'status', 'asked_at', 'answered_at'];

    protected function casts(): array
    {
        return [
            'difficulty' => QuestionDifficulty::class,
            'status' => QuestionStatus::class,
            'asked_at' => 'datetime',
            'answered_at' => 'datetime',
        ];
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function answer(): HasOne
    {
        return $this->hasOne(Answer::class);
    }

    public function isAnswered(): bool
    {
        return $this->status === QuestionStatus::Answered;
    }
}
