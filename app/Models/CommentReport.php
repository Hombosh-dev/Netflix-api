<?php

namespace App\Models;

use App\Enums\CommentReportType;
use App\Models\Builders\CommentReportQueryBuilder;
use Database\Factories\CommentReportFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

/**
 * @mixin IdeHelperCommentReport
 */
class CommentReport extends Model
{
    /** @use HasFactory<CommentReportFactory> */
    use HasFactory, HasUlids;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return CommentReportQueryBuilder
     */
    public function newEloquentBuilder($query): CommentReportQueryBuilder
    {
        return new CommentReportQueryBuilder($query);
    }

    protected function casts(): array
    {
        return [
            'type' => CommentReportType::class,
            'is_viewed' => 'boolean',
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
