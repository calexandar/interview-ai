<?php

namespace App\Shared\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToOrganization
{
    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    public static function bootBelongsToOrganization(): void
    {
        static::creating(function (Model $model) {
            if ($model->getAttribute('organization_id') === null && auth()->check()) {
                $model->setAttribute('organization_id', auth()->user()->organization_id);
            }
        });
    }
}
