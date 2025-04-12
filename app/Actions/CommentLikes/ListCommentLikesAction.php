<?php

namespace App\Actions\CommentLikes;

use App\Models\CommentLike;
use Illuminate\Database\Eloquent\Collection;

class ListCommentLikesAction
{
    public function execute(): Collection
    {
        return CommentLike::with(['user', 'comment'])->get();
    }
}
