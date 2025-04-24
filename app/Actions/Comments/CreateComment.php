<?php

namespace App\Actions\Comments;

use App\DTOs\Comments\CommentStoreDTO;
use App\Models\Comment;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateComment
{
    use AsAction;

    /**
     * Create a new comment.
     *
     * @param  CommentStoreDTO  $dto
     * @return Comment
     */
    public function handle(CommentStoreDTO $dto): Comment
    {
        $comment = new Comment();
        $comment->user_id = $dto->userId;
        $comment->commentable_type = $dto->commentableType;
        $comment->commentable_id = $dto->commentableId;
        $comment->body = $dto->body;
        $comment->is_spoiler = $dto->isSpoiler;
        $comment->parent_id = $dto->parentId;
        $comment->save();
        
        return $comment->load(['user'])->loadCount(['likes', 'children']);
    }
}
