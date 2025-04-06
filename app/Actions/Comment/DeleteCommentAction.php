<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteCommentAction
{
    /**
     * Видаляє запис Comment.
     *
     * @param Comment $comment
     * @return bool|null
     */
    public function execute(Comment $comment): ?bool
    {
        Gate::authorize('delete', $comment);
        return $comment->delete();
    }
}
