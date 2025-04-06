<?php

namespace App\Actions\CommentLike;

use App\Models\CommentLike;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCommentLikeAction
{
    /**
     * Оновлює існуючий запис CommentLike.
     *
     * @param CommentLike $commentLike
     * @param array{
     *     is_liked?: bool
     * } $data
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(CommentLike $commentLike, array $data): bool
    {
        Gate::forUser($commentLike->user)->authorize('update', $commentLike);
        return $commentLike->update($data);
    }
}
