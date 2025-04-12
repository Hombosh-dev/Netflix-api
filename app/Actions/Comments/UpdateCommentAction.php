<?php

namespace App\Actions\Comments;

use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class UpdateCommentAction
{
    public function execute(Comment $comment, UpdateCommentRequest $request): Comment
    {
        return DB::transaction(function () use ($comment, $request) {
            $comment->update($request->validated());
            return $comment->fresh();
        });
    }
}
