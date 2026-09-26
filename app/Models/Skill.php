<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Pivot|null $pivot Only present
 *                                                                       when the skill was
 *                                                                       loaded through a
 *                                                                       position.
 */
class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * @return BelongsToMany<Position, $this>
     */
    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class)->withPivot('weight', 'required')->withTimestamps();
    }

    /**
     * @return HasMany<Question, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * @return HasMany<SkillAssessment, $this>
     */
    public function skillAssessments(): HasMany
    {
        return $this->hasMany(SkillAssessment::class);
    }
}
