<?php

namespace App\Http\Controllers;

use App\Actions\Comment\CreateCommentAction;
use App\Actions\Comment\DeleteCommentAction;
use App\Actions\Comment\ReadCommentAction;
use App\Actions\Comment\UpdateCommentAction;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Повертає колекцію всіх записів Comment.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $comments = Comment::all();
        return CommentResource::collection($comments);
    }

    /**
     * Зберігає новий запис Comment.
     */
    public function store(CreateCommentRequest $request, CreateCommentAction $createComment): CommentResource
    {
        $comment = $createComment->execute($request->validated());
        return new CommentResource($comment);
    }

    /**
     * Повертає дані конкретного Comment.
     */
    public function show(Comment $comment, ReadCommentAction $getComment): CommentResource
    {
        return new CommentResource($comment);
    }

    /**
     * Оновлює дані конкретного Comment.
     */
    public function update(UpdateCommentRequest $request, Comment $comment, UpdateCommentAction $updateComment): CommentResource
    {
        $updateComment->execute($comment, $request->validated());
        return new CommentResource($comment);
    }

    /**
     * Видаляє запис Comment.
     */
    public function destroy(Comment $comment, DeleteCommentAction $deleteComment): \Illuminate\Http\Response
    {
        $deleteComment->execute($comment);
        return response()->noContent();
    }
}
