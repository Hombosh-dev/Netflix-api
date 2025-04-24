<?php

namespace App\Actions\CommentReports;

use App\DTOs\CommentReports\CommentReportUpdateDTO;
use App\Models\CommentReport;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCommentReport
{
    use AsAction;

    /**
     * Update an existing comment report.
     *
     * @param  CommentReport  $commentReport
     * @param  CommentReportUpdateDTO  $dto
     * @return CommentReport
     */
    public function handle(CommentReport $commentReport, CommentReportUpdateDTO $dto): CommentReport
    {
        // Update the comment report
        if ($dto->isViewed !== null) {
            $commentReport->is_viewed = $dto->isViewed;
        }
        
        if ($dto->type !== null) {
            $commentReport->type = $dto->type;
        }
        
        if ($dto->body !== null) {
            $commentReport->body = $dto->body;
        }
        
        $commentReport->save();
        
        return $commentReport->load(['user', 'comment']);
    }
}
