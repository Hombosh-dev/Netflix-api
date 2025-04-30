<?php

namespace App\Http\Controllers;

use App\Actions\CommentLikes\CreateCommentLike;
use App\Actions\CommentLikes\GetCommentLikes;
use App\DTOs\CommentLikes\CommentLikeIndexDTO;
use App\DTOs\CommentLikes\CommentLikeStoreDTO;
use App\DTOs\CommentLikes\CommentLikeUpdateDTO;
use App\Http\Requests\CommentLikes\CommentLikeDeleteRequest;
use App\Http\Requests\CommentLikes\CommentLikeIndexRequest;
use App\Http\Requests\CommentLikes\CommentLikeStoreRequest;
use App\Http\Requests\CommentLikes\CommentLikeUpdateRequest;
use App\Http\Resources\CommentLikeResource;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentLikeController extends Controller
{
    /**
     * Get paginated list of comment likes with filtering, sorting and pagination
     *
     * @param  CommentLikeIndexRequest  $request
     * @param  GetCommentLikes  $action
     * @return AnonymousResourceCollection
     */
    public function index(CommentLikeIndexRequest $request, GetCommentLikes $action): AnonymousResourceCollection
    {
        // Перевіряємо, чи користувач авторизований
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        $dto = CommentLikeIndexDTO::fromRequest($request);
        $commentLikes = $action->handle($dto);

        return CommentLikeResource::collection($commentLikes);
    }

    /**
     * Get detailed information about a specific comment like
     *
     * @param  CommentLike  $commentLike
     * @return CommentLikeResource
     */
    public function show(CommentLike $commentLike): CommentLikeResource
    {
        // Перевіряємо, чи користувач авторизований
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        return new CommentLikeResource($commentLike->load(['user', 'comment']));
    }

    /**
     * Store a newly created comment like
     *
     * @param  CommentLikeStoreRequest  $request
     * @param  CreateCommentLike  $action
     * @return CommentLikeResource|JsonResponse
     */
    public function store(CommentLikeStoreRequest $request, CreateCommentLike $action): CommentLikeResource|JsonResponse
    {
        // Перевіряємо, чи користувач авторизований
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        $dto = CommentLikeStoreDTO::fromRequest($request);

        // Check if the user has already liked this comment
        $existingLike = CommentLike::where('user_id', $request->user()->id)
            ->where('comment_id', $request->input('comment_id'))
            ->first();

        if ($existingLike) {
            // If we want to update the existing like (e.g., change from like to dislike)
            if ($existingLike->is_liked !== $request->boolean('is_liked', true)) {
                $existingLike->is_liked = $request->boolean('is_liked', true);
                $existingLike->save();

                return new CommentLikeResource($existingLike->load(['user', 'comment']));
            }

            return response()->json(['message' => 'You have already liked this comment'], 422);
        }

        $commentLike = $action->handle($dto);

        return new CommentLikeResource($commentLike->load(['user', 'comment']));
    }

    /**
     * Update the specified comment like
     *
     * @param  CommentLikeUpdateRequest  $request
     * @param  CommentLike  $commentLike
     * @return CommentLikeResource
     */
    public function update(CommentLikeUpdateRequest $request, CommentLike $commentLike): CommentLikeResource
    {
        // Перевіряємо, чи користувач авторизований
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        // Перевіряємо, чи користувач має право оновлювати цей лайк
        if (auth()->id() !== $commentLike->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to update this like');
        }

        $dto = CommentLikeUpdateDTO::fromRequest($request);

        // Update the comment like
        if ($dto->isLiked !== null) {
            $commentLike->is_liked = $dto->isLiked;
        }

        $commentLike->save();

        return new CommentLikeResource($commentLike->load(['user', 'comment']));
    }

    /**
     * Remove the specified comment like
     *
     * @param  CommentLikeDeleteRequest  $request
     * @param  CommentLike  $commentLike
     * @return JsonResponse
     */
    public function destroy(CommentLikeDeleteRequest $request, CommentLike $commentLike): JsonResponse
    {
        // Перевіряємо, чи користувач авторизований
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        // Перевіряємо, чи користувач має право видаляти цей лайк
        if (auth()->id() !== $commentLike->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to delete this like');
        }

        $commentLike->delete();

        return response()->json(['message' => 'Like removed successfully']);
    }

    /**
     * Get likes for a specific comment
     *
     * @param  Comment  $comment
     * @param  CommentLikeIndexRequest  $request
     * @param  GetCommentLikes  $action
     * @return AnonymousResourceCollection
     */
    public function forComment(Comment $comment, CommentLikeIndexRequest $request, GetCommentLikes $action): AnonymousResourceCollection
    {
        // Перевіряємо, чи користувач авторизований
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        $request->merge(['comment_id' => $comment->id]);
        $dto = CommentLikeIndexDTO::fromRequest($request);
        $commentLikes = $action->handle($dto);

        return CommentLikeResource::collection($commentLikes);
    }

    /**
     * Get likes by a specific user
     *
     * @param  User  $user
     * @param  CommentLikeIndexRequest  $request
     * @param  GetCommentLikes  $action
     * @return AnonymousResourceCollection
     */
    public function forUser(User $user, CommentLikeIndexRequest $request, GetCommentLikes $action): AnonymousResourceCollection
    {
        // Перевіряємо, чи користувач авторизований
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        // Перевіряємо, чи користувач має право переглядати лайки іншого користувача
        if (auth()->id() !== $user->id && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view likes for this user');
        }

        $request->merge(['user_id' => $user->id]);
        $dto = CommentLikeIndexDTO::fromRequest($request);
        $commentLikes = $action->handle($dto);

        return CommentLikeResource::collection($commentLikes);
    }
}
