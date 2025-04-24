<?php

namespace App\Actions\CommentLikes;

use App\DTOs\CommentLikes\CommentLikeStoreDTO;
use App\Models\CommentLike;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCommentLike
{
    use AsAction;

    /**
     * Create a new comment like.
     *
     * @param  CommentLikeStoreDTO  $dto
     * @return CommentLike
     */
    public function handle(CommentLikeStoreDTO $dto): CommentLike
    {
        // Check if comment like already exists
        $existingLike = CommentLike::where('user_id', $dto->userId)
            ->where('comment_id', $dto->commentId)
            ->first();
            
        if ($existingLike) {
            // Update existing like
            $existingLike->is_liked = $dto->isLiked;
            $existingLike->save();
            
            return $existingLike;
        }
        
        // Create new comment like
        $commentLike = new CommentLike();
        $commentLike->user_id = $dto->userId;
        $commentLike->comment_id = $dto->commentId;
        $commentLike->is_liked = $dto->isLiked;
        $commentLike->save();
        
        return $commentLike;
    }
}
