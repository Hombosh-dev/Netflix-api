<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;

class ListRecentCommentsAction
{
    public function execute(string $sort = 'recent', int $perPage = 10): LengthAwarePaginator
    {
        $query = Comment::query()
            ->with(['user', 'parent']) // Eager loading зв’язків
            ->when($sort === 'recent', fn($q) => $q->latest())
            ->when($sort === 'popular', fn($q) => $q->withCount('likes')->orderBy('likes_count', 'desc'));

        return $query->paginate($perPage);
    }
}
