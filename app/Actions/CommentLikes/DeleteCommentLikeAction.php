<?php

namespace App\Actions\CommentLikes;

use App\Models\CommentLike;
use Illuminate\Support\Facades\DB;

class DeleteCommentLikeAction
{
    public function execute(CommentLike $commentLike): void
    {
        DB::transaction(function () use ($commentLike) {
            $commentLike->delete();
        });
    }
}
