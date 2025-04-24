<?php

namespace App\Actions\Comments;

use App\DTOs\Comments\CommentUpdateDTO;
use App\Models\Comment;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateComment
{
    use AsAction;

    /**
     * Update an existing comment.
     *
     * @param  Comment  $comment
     * @param  CommentUpdateDTO  $dto
     * @return Comment
     */
    public function handle(Comment $comment, CommentUpdateDTO $dto): Comment
    {
        $comment->body = $dto->body;
        
        if ($dto->isSpoiler !== null) {
            $comment->is_spoiler = $dto->isSpoiler;
        }
        
        $comment->save();
        
        return $comment->load(['user'])->loadCount(['likes', 'children']);
    }
}
