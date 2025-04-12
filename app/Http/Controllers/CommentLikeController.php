<?php

namespace App\Http\Controllers;

use App\Actions\CommentLikes\CreateCommentLikeAction;
use App\Actions\CommentLikes\DeleteCommentLikeAction;
use App\Actions\CommentLikes\ListByCommentAction;
use App\Actions\CommentLikes\ListByUserAction;
use App\Actions\CommentLikes\ListCommentLikesAction;
use App\Actions\CommentLikes\ListOnlyDislikesAction;
use App\Actions\CommentLikes\ListOnlyLikesAction;
use App\Actions\CommentLikes\ShowCommentLikeAction;
use App\Http\Requests\CommentLike\StoreCommentLikeRequest;
use App\Http\Resources\CommentLike\CommentLikeCollection;
use App\Http\Resources\CommentLike\CommentLikeResource;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class CommentLikeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('throttle:100,1440', only: ['store']), // 100 запитів на день (1440 хвилин)
        ];
    }

    public function index(ListCommentLikesAction $action)
    {
        return new CommentLikeCollection($action->execute());
    }

    public function store(StoreCommentLikeRequest $request, CreateCommentLikeAction $action)
    {
        Gate::authorize('create', CommentLike::class);
        return new CommentLikeResource($action->execute($request));
    }

    public function show(CommentLike $commentLike, ShowCommentLikeAction $action)
    {
        return new CommentLikeResource($action->execute($commentLike));
    }

    public function destroy(CommentLike $commentLike, DeleteCommentLikeAction $action)
    {
        Gate::authorize('delete', $commentLike);
        $action->execute($commentLike);
        return response()->noContent();
    }

    public function byUser(User $user, ListByUserAction $action)
    {
        return new CommentLikeCollection($action->execute($user));
    }

    public function byComment(Comment $comment, ListByCommentAction $action)
    {
        return new CommentLikeCollection($action->execute($comment));
    }

    public function onlyLikes(ListOnlyLikesAction $action)
    {
        return new CommentLikeCollection($action->execute());
    }

    public function onlyDislikes(ListOnlyDislikesAction $action)
    {
        return new CommentLikeCollection($action->execute());
    }
}
