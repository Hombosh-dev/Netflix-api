<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;


/**
 * Клас для створення нового запису Comment.
 *
 * @param array{
 *     commentable_type: string,
 *     commentable_id: string,
 *     user_id: string,
 *     is_spoiler?: bool,
 *     body: string,
 *     parent_id?: string|null
 * } $data
 */
class CreateCommentAction
{
    /**
     * Виконує створення нового запису Comment.
     *
     * @param array $data
     * @return Comment
     * @throws AuthorizationException
     */
    public function execute(array $data): Comment
    {
        Gate::authorize('create', Comment::class);
        return Comment::create($data);
    }
}
