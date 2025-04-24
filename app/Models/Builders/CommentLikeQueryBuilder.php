<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class CommentLikeQueryBuilder extends Builder
{
    /**
     * Filter by user.
     *
     * @param string $userId
     * @return self
     */
    public function byUser(string $userId): self
    {
        return $this->where('user_id', $userId);
    }
    
    /**
     * Filter by comment.
     *
     * @param string $commentId
     * @return self
     */
    public function byComment(string $commentId): self
    {
        return $this->where('comment_id', $commentId);
    }
    
    /**
     * Get only likes.
     *
     * @return self
     */
    public function onlyLikes(): self
    {
        return $this->where('is_liked', true);
    }
    
    /**
     * Get only dislikes.
     *
     * @return self
     */
    public function onlyDislikes(): self
    {
        return $this->where('is_liked', false);
    }
}
