<?php

namespace App\Http\Controllers;

use App\Actions\Comments\CreateCommentAction;
use App\Actions\Comments\DeleteCommentAction;
use App\Actions\Comments\ListChildrenAction;
use App\Actions\Comments\ListLikesAction;
use App\Actions\Comments\ListRecentCommentsAction;
use App\Actions\Comments\ListRepliesAction;
use App\Actions\Comments\ListReportsAction;
use App\Actions\Comments\ListRootsAction;
use App\Actions\Comments\ShowCommentAction;
use App\Actions\Comments\UpdateCommentAction;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index(ListRecentCommentsAction $action)
    {
        $sort = request('sort', 'recent'); // 'recent' або 'popular'
        $perPage = request('per_page', 10);
        return CommentResource::collection($action->execute($sort, $perPage));
    }

    public function store(StoreCommentRequest $request, CreateCommentAction $action)
    {
        return new CommentResource($action->execute($request));
    }

    public function show(Comment $comment, ShowCommentAction $action)
    {
        return new CommentResource($action->execute($comment));
    }

    public function update(Comment $comment, UpdateCommentRequest $request, UpdateCommentAction $action)
    {
        return new CommentResource($action->execute($comment, $request));
    }

    public function destroy(Comment $comment, DeleteCommentAction $action)
    {
        $action->execute($comment);
        return response()->noContent();
    }

    public function replies(ListRepliesAction $action)
    {
        return CommentResource::collection($action->execute());
    }

    public function roots(ListRootsAction $action)
    {
        return CommentResource::collection($action->execute());
    }

    public function likes(Comment $comment, ListLikesAction $action)
    {
        return $action->execute($comment); // Окремий ресурс для лайків
    }

    public function reports(Comment $comment, ListReportsAction $action)
    {
        return $action->execute($comment); // Окремий ресурс для скарг
    }

    public function children(Comment $comment, ListChildrenAction $action)
    {
        return CommentResource::collection($action->execute($comment));
    }
}
