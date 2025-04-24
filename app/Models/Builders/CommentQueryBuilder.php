<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class CommentQueryBuilder extends Builder
{
    /**
     * Get only reply comments.
     *
     * @return self
     */
    public function replies(): self
    {
        return $this->whereNotNull('parent_id');
    }
    
    /**
     * Get only root comments.
     *
     * @return self
     */
    public function roots(): self
    {
        return $this->whereNull('parent_id');
    }
    
    /**
     * Get comments for a specific user.
     *
     * @param string $userId
     * @return self
     */
    public function forUser(string $userId): self
    {
        return $this->where('user_id', $userId);
    }
    
    /**
     * Get comments for a specific commentable.
     *
     * @param string $commentableType
     * @param string $commentableId
     * @return self
     */
    public function forCommentable(string $commentableType, string $commentableId): self
    {
        return $this->where('commentable_type', $commentableType)
            ->where('commentable_id', $commentableId);
    }
    
    /**
     * Get comments with spoilers.
     *
     * @return self
     */
    public function withSpoilers(): self
    {
        return $this->where('is_spoiler', true);
    }
    
    /**
     * Get comments without spoilers.
     *
     * @return self
     */
    public function withoutSpoilers(): self
    {
        return $this->where('is_spoiler', false);
    }
    
    /**
     * Get comments with the most likes.
     *
     * @param int $limit
     * @return self
     */
    public function mostLiked(int $limit = 10): self
    {
        return $this->withCount('likes')
            ->orderByDesc('likes_count')
            ->limit($limit);
    }
}
