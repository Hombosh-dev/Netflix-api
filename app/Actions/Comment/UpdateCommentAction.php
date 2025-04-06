<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCommentAction
{
    /**
     * Оновлює існуючий запис Comment.
     *
     * @param Comment $comment
     * @param array{
     *     commentable_type?: string,
     *     commentable_id?: string,
     *     user_id?: string,
     *     is_spoiler?: bool,
     *     body?: string,
     *     parent_id?: string|null
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(Comment $comment, array $data): bool
    {
        Gate::authorize('update', $comment);
        return $comment->update($data);
    }
}
