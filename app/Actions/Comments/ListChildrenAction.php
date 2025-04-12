<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class ListChildrenAction
{
    public function execute(Comment $comment): Collection
    {
        return $comment->children()->with(['user'])->get();
    }
}
