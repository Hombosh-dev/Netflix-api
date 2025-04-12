<?php

namespace App\Actions\CommentLikes;

use App\Models\CommentLike;
use Illuminate\Database\Eloquent\Collection;

class ListOnlyLikesAction
{
    public function execute(): Collection
    {
        return CommentLike::onlyLikes()->with(['user', 'comment'])->get();
    }
}
