<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property string $id
 * @property string $commentable_type
 * @property string $commentable_id
 * @property string $user_id
 * @property bool $is_spoiler
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $children
 * @property-read int|null $children_count
 * @property-read Model|\Eloquent $commentable
 * @property-read mixed $is_reply
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentLike> $likes
 * @property-read int|null $likes_count
 * @property-read Comment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentReport> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\CommentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Comment newModelQuery()
 * @method static Builder<static>|Comment newQuery()
 * @method static Builder<static>|Comment query()
 * @method static Builder<static>|Comment replies()
 * @method static Builder<static>|Comment roots()
 * @method static Builder<static>|Comment whereBody($value)
 * @method static Builder<static>|Comment whereCommentableId($value)
 * @method static Builder<static>|Comment whereCommentableType($value)
 * @method static Builder<static>|Comment whereCreatedAt($value)
 * @method static Builder<static>|Comment whereId($value)
 * @method static Builder<static>|Comment whereIsSpoiler($value)
 * @method static Builder<static>|Comment whereParentId($value)
 * @method static Builder<static>|Comment whereUpdatedAt($value)
 * @method static Builder<static>|Comment whereUserId($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory, HasUlids;

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class)->chaperone();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(CommentReport::class)->chaperone();
    }

    public function scopeReplies(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    public function childrenCount(): int
    {
        return $this->children()->count();
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->chaperone();
    }

    public function excerpt(int $length = 50): string
    {
        return str()->limit($this->body, $length);
    }

    protected function isReply(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->parent_id !== null
        );
    }

    protected function body(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => trim($value)
        );
    }
}
