<?php

namespace App\Models;

use App\Enums\UserListType;
use App\Models\Builders\UserListQueryBuilder;
use Database\Factories\UserListFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder;

/**
 * @mixin IdeHelperUserList
 */
class UserList extends Model
{
    /** @use HasFactory<UserListFactory> */
    use HasFactory, HasUlids;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return UserListQueryBuilder
     */
    public function newEloquentBuilder($query): UserListQueryBuilder
    {
        return new UserListQueryBuilder($query);
    }

    protected function casts(): array
    {
        return [
            'type' => UserListType::class,
        ];
    }

    public function listable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
