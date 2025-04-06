<?php

namespace App\Models;

use App\Enums\UserListType;
use Database\Factories\UserListsFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property string $id
 * @property string $user_id
 * @property string $listable_type
 * @property string $listable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property UserListType $type
 * @property-read Model|\Eloquent $listable
 * @property-read \App\Models\User $user
 * @method static Builder<static>|UserList forUser(string $userId, ?string $listableClass = null, ?\App\Enums\UserListType $userListType = null)
 * @method static Builder<static>|UserList newModelQuery()
 * @method static Builder<static>|UserList newQuery()
 * @method static Builder<static>|UserList ofType(\App\Enums\UserListType $type)
 * @method static Builder<static>|UserList query()
 * @method static Builder<static>|UserList whereCreatedAt($value)
 * @method static Builder<static>|UserList whereId($value)
 * @method static Builder<static>|UserList whereListableId($value)
 * @method static Builder<static>|UserList whereListableType($value)
 * @method static Builder<static>|UserList whereType($value)
 * @method static Builder<static>|UserList whereUpdatedAt($value)
 * @method static Builder<static>|UserList whereUserId($value)
 * @mixin \Eloquent
 */
class UserList extends Model
{
    /** @use HasFactory<UserListsFactory> */
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

    public function scopeForUser(Builder $query,
                                 string $userId,
                                 ?string $listableClass = null,
                                 ?UserListType $userListType = null): Builder
    {
        return $query->where('user_id', $userId)
            ->when($listableClass, function ($query) use ($listableClass) {
                $query->where('listable_type', $listableClass);
            })
            ->when($userListType, function ($query) use ($userListType) {
                $query->where('type', $userListType->value);
            });
    }
}
