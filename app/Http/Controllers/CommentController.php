<?php

namespace App\Http\Controllers;

use App\Actions\Comments\CreateComment;
use App\Actions\Comments\GetCommentDetails;
use App\Actions\Comments\GetCommentLikes;
use App\Actions\Comments\GetComments;
use App\Actions\Comments\GetRecentComments;
use App\Actions\Comments\UpdateComment;
use App\DTOs\Comments\CommentIndexDTO;
use App\DTOs\Comments\CommentRecentDTO;
use App\DTOs\Comments\CommentStoreDTO;
use App\DTOs\Comments\CommentUpdateDTO;
use App\Http\Requests\Comments\CommentDeleteRequest;
use App\Http\Requests\Comments\CommentIndexRequest;
use App\Http\Requests\Comments\CommentStoreRequest;
use App\Http\Requests\Comments\CommentUpdateRequest;
use App\Http\Resources\CommentLikeResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\UserCommentResource;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    /**
     * Get paginated list of comments with filtering, sorting and pagination
     *
     * @param  CommentIndexRequest  $request
     * @param  GetComments  $action
     * @return AnonymousResourceCollection
     */
    public function index(CommentIndexRequest $request, GetComments $action): AnonymousResourceCollection
    {
        $dto = CommentIndexDTO::fromRequest($request);
        $comments = $action->handle($dto);

        return CommentResource::collection($comments);
    }

    /**
     * Get detailed information about a specific comment
     *
     * @param  Comment  $comment
     * @param  GetCommentDetails  $action
     * @return CommentResource
     */
    public function show(Comment $comment, GetCommentDetails $action): CommentResource
    {
        $comment = $action->handle($comment);

        return new CommentResource($comment);
    }

    /**
     * Get replies to a specific comment
     *
     * @param  Comment  $comment
     * @param  CommentIndexRequest  $request
     * @param  GetComments  $action
     * @return AnonymousResourceCollection
     */
    public function replies(Comment $comment, CommentIndexRequest $request, GetComments $action): AnonymousResourceCollection
    {
        $request->merge(['parent_id' => $comment->id]);
        $dto = CommentIndexDTO::fromRequest($request);
        $replies = $action->handle($dto);

        return CommentResource::collection($replies);
    }

    /**
     * Get likes for a specific comment
     *
     * @param  Comment  $comment
     * @param  GetCommentLikes  $action
     * @return AnonymousResourceCollection
     */
    public function likes(Comment $comment, GetCommentLikes $action): AnonymousResourceCollection
    {
        $likes = $action->handle($comment);

        return CommentLikeResource::collection($likes);
    }

    /**
     * Get recent comments
     *
     * @param  Request  $request
     * @param  GetRecentComments  $action
     * @return AnonymousResourceCollection
     */
    public function recent(Request $request, GetRecentComments $action): AnonymousResourceCollection
    {
        $dto = CommentRecentDTO::fromRequest($request);
        $comments = $action->handle($dto);

        return CommentResource::collection($comments);
    }

    /**
     * Get root comments for a specific commentable
     *
     * @param  string  $commentableType
     * @param  string  $commentableId
     * @param  CommentIndexRequest  $request
     * @param  GetComments  $action
     * @return AnonymousResourceCollection
     *
     * @urlParam commentable_type required The type of the commentable. Example: movie, episode, selection
     * @urlParam commentable_id required The ID of the commentable. Example: 01HN5PXMEH6SDMF0KAVSW1DYTY
     */
    public function roots(string $commentableType, string $commentableId, CommentIndexRequest $request, GetComments $action): AnonymousResourceCollection
    {
        // Convert commentable_type to full class name if needed
        $commentableType = match ($commentableType) {
            'movie' => 'App\\Models\\Movie',
            'episode' => 'App\\Models\\Episode',
            'selection' => 'App\\Models\\Selection',
            default => $commentableType
        };

        $request->merge([
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'is_root' => true
        ]);

        $dto = CommentIndexDTO::fromRequest($request);
        $comments = $action->handle($dto);

        return CommentResource::collection($comments);
    }

    /**
     * Get comments for a specific user
     *
     * @param  User  $user
     * @param  CommentIndexRequest  $request
     * @param  GetComments  $action
     * @return AnonymousResourceCollection
     */
    public function forUser(User $user, CommentIndexRequest $request, GetComments $action): AnonymousResourceCollection
    {
        $request->merge(['user_id' => $user->id]);
        $dto = CommentIndexDTO::fromRequest($request);
        $comments = $action->handle($dto);

        return UserCommentResource::collection($comments);
    }

    /**
     * Store a newly created comment
     *
     * @param  CommentStoreRequest  $request
     * @param  CreateComment  $action
     * @return CommentResource
     * @authenticated
     */
    public function store(CommentStoreRequest $request, CreateComment $action): CommentResource
    {
        $dto = CommentStoreDTO::fromRequest($request);
        $comment = $action->handle($dto);

        return new CommentResource($comment);
    }

    /**
     * Update the specified comment
     *
     * @param  CommentUpdateRequest  $request
     * @param  Comment  $comment
     * @param  UpdateComment  $action
     * @return CommentResource
     * @authenticated
     */
    public function update(CommentUpdateRequest $request, Comment $comment, UpdateComment $action): CommentResource
    {
        // Перевіряємо, чи користувач має право оновлювати цей коментар
        if (auth()->id() !== $comment->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to update this comment');
        }

        $dto = CommentUpdateDTO::fromRequest($request);
        $comment = $action->handle($comment, $dto);

        return new CommentResource($comment);
    }

    /**
     * Remove the specified comment
     *
     * @param  CommentDeleteRequest  $request
     * @param  Comment  $comment
     * @return JsonResponse
     * @authenticated
     */
    public function destroy(CommentDeleteRequest $request, Comment $comment): JsonResponse
    {
        // Перевіряємо, чи користувач має право видаляти цей коментар
        if (auth()->id() !== $comment->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to delete this comment');
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
