<?php

namespace App\Models;

use App\Models\Builders\CommentLikeQueryBuilder;
use Database\Factories\CommentLikeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

/**
 * @mixin IdeHelperCommentLike
 */
class CommentLike extends Model
{
    /** @use HasFactory<CommentLikeFactory> */
    use HasFactory, HasUlids;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return CommentLikeQueryBuilder
     */
    public function newEloquentBuilder($query): CommentLikeQueryBuilder
    {
        return new CommentLikeQueryBuilder($query);
    }

    protected function casts(): array
    {
        return [
            'is_liked' => 'boolean',
        ];
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
