<?php

namespace App\Actions\CommentLikes;

use App\Models\CommentLike;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ListByUserAction
{
    public function execute(User $user): Collection
    {
        return CommentLike::byUser($user->id)->with(['comment'])->get();
    }
}
