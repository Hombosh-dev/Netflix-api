<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCommentDetails
{
    use AsAction;

    /**
     * Get detailed information about a specific comment.
     *
     * @param  Comment  $comment
     * @return Comment
     */
    public function handle(Comment $comment): Comment
    {
        return $comment->load(['user', 'parent', 'commentable'])->loadCount(['likes', 'children']);
    }
}
