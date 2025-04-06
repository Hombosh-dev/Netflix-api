<?php

namespace App\Models;

use Database\Factories\CommentLikeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Comment|null $comment
 * @property-read \App\Models\User|null $user
 * @method static Builder<static>|CommentLike byComment(string $commentId)
 * @method static Builder<static>|CommentLike byUser(string $userId)
 * @method static \Database\Factories\CommentLikeFactory factory($count = null, $state = [])
 * @method static Builder<static>|CommentLike newModelQuery()
 * @method static Builder<static>|CommentLike newQuery()
 * @method static Builder<static>|CommentLike onlyDislikes()
 * @method static Builder<static>|CommentLike onlyLikes()
 * @method static Builder<static>|CommentLike query()
 * @method static Builder<static>|CommentLike whereCreatedAt($value)
 * @method static Builder<static>|CommentLike whereId($value)
 * @method static Builder<static>|CommentLike whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CommentLike extends Model
{
    /** @use HasFactory<CommentLikeFactory> */
    use HasFactory, HasUlids;

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByComment(Builder $query, string $commentId): Builder
    {
        return $query->where('comment_id', $commentId);
    }

    public function scopeOnlyLikes(Builder $query): Builder
    {
        return $query->where('is_liked', true);
    }

    public function scopeOnlyDislikes(Builder $query): Builder
    {
        return $query->where('is_liked', false);
    }
}
