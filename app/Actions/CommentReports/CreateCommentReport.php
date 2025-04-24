<?php

namespace App\Actions\CommentReports;

use App\DTOs\CommentReports\CommentReportStoreDTO;
use App\Models\CommentReport;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCommentReport
{
    use AsAction;

    /**
     * Create a new comment report.
     *
     * @param  CommentReportStoreDTO  $dto
     * @return CommentReport
     */
    public function handle(CommentReportStoreDTO $dto): CommentReport
    {
        // Check if report already exists
        $existingReport = CommentReport::where('user_id', $dto->userId)
            ->where('comment_id', $dto->commentId)
            ->where('type', $dto->type)
            ->first();
            
        if ($existingReport) {
            // Update existing report if body is provided
            if ($dto->body !== null) {
                $existingReport->body = $dto->body;
                $existingReport->save();
            }
            
            return $existingReport;
        }
        
        // Create new comment report
        $commentReport = new CommentReport();
        $commentReport->user_id = $dto->userId;
        $commentReport->comment_id = $dto->commentId;
        $commentReport->type = $dto->type;
        $commentReport->body = $dto->body;
        $commentReport->is_viewed = false; // New reports are not viewed by default
        $commentReport->save();
        
        return $commentReport;
    }
}
