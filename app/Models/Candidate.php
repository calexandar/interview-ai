<?php

namespace App\Models;

use App\Shared\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Candidate extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = ['organization_id', 'name', 'email'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
