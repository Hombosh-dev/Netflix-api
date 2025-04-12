<?php

namespace App\Actions\CommentLikes;

use App\Models\CommentLike;
use Illuminate\Database\Eloquent\Collection;

class ListOnlyDislikesAction
{
    public function execute(): Collection
    {
        return CommentLike::onlyDislikes()->with(['user', 'comment'])->get();
    }
}
