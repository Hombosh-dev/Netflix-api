<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class DeleteCommentAction
{
    public function execute(Comment $comment): void
    {
        DB::transaction(function () use ($comment) {
            $comment->delete();
        });
    }
}
