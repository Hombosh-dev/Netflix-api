<?php

namespace App\Models;

use App\Enums\CommentReportType;
use Database\Factories\CommentReportFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property string $id
 * @property string $comment_id
 * @property string $user_id
 * @property bool $is_liked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property CommentReportType $type
 * @property-read \App\Models\Comment $comment
 * @property-read \App\Models\User $user
 * @method static Builder<static>|CommentReport byComment(string $commentId)
 * @method static Builder<static>|CommentReport byUser(string $userId)
 * @method static \Database\Factories\CommentReportFactory factory($count = null, $state = [])
 * @method static Builder<static>|CommentReport newModelQuery()
 * @method static Builder<static>|CommentReport newQuery()
 * @method static Builder<static>|CommentReport query()
 * @method static Builder<static>|CommentReport unViewed()
 * @method static Builder<static>|CommentReport whereCommentId($value)
 * @method static Builder<static>|CommentReport whereCreatedAt($value)
 * @method static Builder<static>|CommentReport whereId($value)
 * @method static Builder<static>|CommentReport whereIsLiked($value)
 * @method static Builder<static>|CommentReport whereUpdatedAt($value)
 * @method static Builder<static>|CommentReport whereUserId($value)
 * @mixin \Eloquent
 */
class CommentReport extends Model
{
    /** @use HasFactory<CommentReportFactory> */
    use HasFactory, HasUlids;

    protected $casts = [
        'type' => CommentReportType::class,
    ];

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

    public function scopeUnViewed(Builder $query): Builder
    {
        return $query->where('is_viewed', false);
    }
}
