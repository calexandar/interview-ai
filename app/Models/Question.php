<?php

namespace App\Models;

use App\Shared\Enums\QuestionDifficulty;
use App\Shared\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $skill_id
 * @property QuestionType $type
 * @property QuestionDifficulty $difficulty
 * @property string $question
 * @property array|null $expected_topics
 * @property string|null $evaluation_guidance
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = ['skill_id', 'type', 'difficulty', 'question', 'expected_topics', 'evaluation_guidance', 'is_active'];

    protected function casts(): array
    {
        return [
            'type' => QuestionType::class,
            'difficulty' => QuestionDifficulty::class,
            'expected_topics' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function interviewQuestions(): HasMany
    {
        return $this->hasMany(InterviewQuestion::class);
    }
}
