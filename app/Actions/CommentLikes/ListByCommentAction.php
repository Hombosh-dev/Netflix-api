<?php

namespace App\Actions\CommentLikes;

use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Database\Eloquent\Collection;

class ListByCommentAction
{
    public function execute(Comment $comment): Collection
    {
        return CommentLike::byComment($comment->id)->with(['user'])->get();
    }
}
