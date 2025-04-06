<?php

namespace App\Actions\CommentLike;

use App\Models\CommentLike;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;


/**
 * Клас для створення нового запису CommentLike.
 *
 * @param array{
 *     comment_id: string,
 *     user_id: string,
 *     is_liked: bool
 * } $data
 */
class CreateCommentLikeAction
{
    /**
     * Виконує створення нового запису CommentLike.
     *
     * @param array $data
     * @return CommentLike
     * @throws AuthorizationException
     */
    public function execute(array $data): CommentLike
    {
        Gate::authorize('create', CommentLike::class);
        return CommentLike::create($data);
    }
}
