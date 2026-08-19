<?php

namespace App\Models;

use App\Shared\Concerns\BelongsToOrganization;
use App\Shared\Enums\PositionLevel;
use App\Shared\Enums\PositionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $organization_id
 * @property string $title
 * @property string|null $description
 * @property PositionLevel $level
 * @property int $duration_minutes
 * @property int $question_count
 * @property PositionStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Position extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = ['organization_id', 'title', 'description', 'level', 'duration_minutes', 'question_count', 'status'];

    protected function casts(): array
    {
        return [
            'level' => PositionLevel::class,
            'status' => PositionStatus::class,
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'position_skills')->withPivot('weight', 'required')->withTimestamps();
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function requiredSkills(): BelongsToMany
    {
        return $this->skills()->wherePivot('required', true);
    }
}
