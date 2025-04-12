<?php

namespace App\Actions\CommentReports;

use App\Models\Comment;
use App\Models\CommentReport;
use Illuminate\Database\Eloquent\Collection;

class ListByCommentAction
{
    public function execute(Comment $comment): Collection
    {
        return CommentReport::where('comment_id', $comment->id)->with(['user'])->get();
    }
}
