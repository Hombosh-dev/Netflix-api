<?php

namespace App\Actions\Comments;

use App\Models\Comment;

class ShowCommentAction
{
    public function execute(Comment $comment): Comment
    {
        return $comment->load(['user', 'parent']); // Eager loading
    }
}
