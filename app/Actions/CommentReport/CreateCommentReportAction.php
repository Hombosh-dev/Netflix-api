<?php

namespace App\Actions\CommentReport;

use App\Models\CommentReport;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

/**
 * Клас для створення нового запису CommentReport.
 *
 * @param array{
 *     comment_id: string,
 *     user_id: string,
 *     type: string
 * } $data
 */
class CreateCommentReportAction
{
    /**
     * Виконує створення нового запису CommentReport.
     *
     * @param array $data
     * @return CommentReport
     * @throws AuthorizationException
     */
    public function execute(array $data): CommentReport
    {
        Gate::authorize('create', CommentReport::class);
        return CommentReport::create($data);
    }
}
