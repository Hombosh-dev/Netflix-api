<?php

namespace App\Actions\CommentLikes;

use App\Models\CommentLike;

class ShowCommentLikeAction
{
    public function execute(CommentLike $commentLike): CommentLike
    {
        return $commentLike->load(['user', 'comment']);
    }
}
