<?php

namespace App\Models;

use App\Enums\UserListType;
use Database\Factories\UserListFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperUserList
 */
class UserList extends Model
{
    /** @use HasFactory<UserListFactory> */
    use HasFactory, HasUlids;

    protected $casts = [
        'type' => UserListType::class,
    ];

    public function listable(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType(Builder $query, UserListType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function scopeForUser(
        Builder $query,
        string $userId,
        ?string $listableClass = null,
        ?UserListType $userListType = null
    ): Builder {
        return $query->where('user_id', $userId)
            ->when($listableClass, function ($query) use ($listableClass) {
                $query->where('listable_type', $listableClass);
            })
            ->when($userListType, function ($query) use ($userListType) {
                $query->where('type', $userListType->value);
            });
    }
}
