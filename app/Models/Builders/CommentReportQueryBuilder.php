<?php

namespace App\Models\Builders;

use App\Enums\CommentReportType;
use Illuminate\Database\Eloquent\Builder;

class CommentReportQueryBuilder extends Builder
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
     * Get unviewed reports.
     *
     * @return self
     */
    public function unViewed(): self
    {
        return $this->where('is_viewed', false);
    }
    
    /**
     * Get viewed reports.
     *
     * @return self
     */
    public function viewed(): self
    {
        return $this->where('is_viewed', true);
    }
    
    /**
     * Filter by report type.
     *
     * @param CommentReportType $type
     * @return self
     */
    public function byType(CommentReportType $type): self
    {
        return $this->where('type', $type->value);
    }
}
