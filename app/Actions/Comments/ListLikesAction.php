<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class ListLikesAction
{
    public function execute(Comment $comment): Collection
    {
        return $comment->likes;
    }
}
