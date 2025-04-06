<?php

namespace App\Http\Controllers;

use App\Actions\CommentLike\CreateCommentLikeAction;
use App\Actions\CommentLike\DeleteCommentLikeAction;
use App\Actions\CommentLike\ReadCommentLikeAction;
use App\Actions\CommentLike\UpdateCommentLikeAction;
use App\Http\Requests\CommentLike\CreateCommentLikeRequest;
use App\Http\Requests\CommentLike\UpdateCommentLikeRequest;
use App\Http\Resources\CommentLikeResource;
use App\Models\CommentLike;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $likes = CommentLike::all();
        return CommentLikeResource::collection($likes);
    }

    /**
     * Зберігає новий запис CommentLike.
     */
    public function store(CreateCommentLikeRequest $request, CreateCommentLikeAction $createCommentLike): CommentLikeResource
    {
        $like = $createCommentLike->execute($request->validated());
        return new CommentLikeResource($like);
    }

    /**
     * Повертає дані конкретного CommentLike.
     */
    public function show(CommentLike $commentLike, ReadCommentLikeAction $getCommentLike): CommentLikeResource
    {
        return new CommentLikeResource($commentLike);
    }

    /**
     * Оновлює дані конкретного CommentLike.
     */
    public function update(UpdateCommentLikeRequest $request, CommentLike $commentLike, UpdateCommentLikeAction $updateCommentLike): CommentLikeResource
    {
        $updateCommentLike->execute($commentLike, $request->validated());
        return new CommentLikeResource($commentLike);
    }

    /**
     * Видаляє запис CommentLike.
     */
    public function destroy(CommentLike $commentLike, DeleteCommentLikeAction $deleteCommentLike): \Illuminate\Http\Response
    {
        $deleteCommentLike->execute($commentLike);
        return response()->noContent();
    }
}
