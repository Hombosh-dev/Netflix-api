<?php

namespace App\Actions\CommentLikes;

use App\Http\Requests\CommentLike\StoreCommentLikeRequest;
use App\Models\CommentLike;
use Illuminate\Support\Facades\DB;

class CreateCommentLikeAction
{
    public function execute(StoreCommentLikeRequest $request): CommentLike
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['user_id'] = auth()->id(); // Додаємо ID авторизованого користувача
            return CommentLike::create($data);
        });
    }
}
