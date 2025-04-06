<?php

namespace App\Actions\CommentLike;

use App\Models\CommentLike;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeleteCommentLikeAction
{
    /**
     * Видаляє запис CommentLike.
     *
     * @param CommentLike $commentLike
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(CommentLike $commentLike): ?bool
    {
        Gate::authorize('delete', $commentLike);
        return $commentLike->delete();
    }
}
