<?php

namespace App\Actions\CommentReports;

use App\Models\CommentReport;
use Illuminate\Support\Facades\DB;

class UpdateCommentReportAction
{
    /**
     * Оновлює скаргу на коментар.
     *
     * @param  CommentReport  $commentReport  Скарга для оновлення
     * @param  array{description?: string|null, is_viewed?: bool|null}  $data  Асоціативний масив із даними для оновлення
     * @return CommentReport
     */
    public function execute(CommentReport $commentReport, array $data): CommentReport
    {
        return DB::transaction(function () use ($commentReport, $data) {
            $commentReport->update($data);
            return $commentReport->fresh();
        });
    }
}
