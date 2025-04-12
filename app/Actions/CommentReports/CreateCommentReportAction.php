<?php

namespace App\Actions\CommentReports;

use App\Enums\CommentReportType;
use App\Models\CommentReport;
use Illuminate\Support\Facades\DB;

class CreateCommentReportAction
{
    /**
     * Створює нову скаргу на коментар.
     *
     * @param  array{comment_id: string, type: CommentReportType, description?: string|null}  $data  Асоціативний масив із даними для створення скарги
     * @return CommentReport
     */
    public function execute(array $data): CommentReport
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = auth()->id(); // Додаємо ID авторизованого користувача
            $data['is_viewed'] = false; // За замовчуванням непереглянута
            return CommentReport::create($data);
        });
    }
}
