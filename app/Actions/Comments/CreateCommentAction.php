<?php

namespace App\Actions\Comments;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Models\Comment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * Дія для створення нового коментаря.
 */
class CreateCommentAction
{
    /**
     * Створює новий коментар із валідаційними даними.
     *
     * @param  StoreCommentRequest  $request  Запит із валідаційними даними для створення коментаря
     * @return Comment Створений коментар
     * @throws AuthorizationException Якщо користувач не має права створювати коментарі
     */
    public function execute(StoreCommentRequest $request): Comment
    {
        Gate::authorize('create', Comment::class);

        return DB::transaction(function () use ($request) {
            return Comment::create($request->validated());
        });
    }
}
