<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BannedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * Exclude banned users from query results.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('is_banned', false);
    }
}
