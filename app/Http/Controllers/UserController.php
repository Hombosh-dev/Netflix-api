<?php

namespace App\Http\Controllers;

use App\Actions\Users\CreateUser;
use App\Actions\Users\GetUsers;
use App\Actions\Users\UpdateUser;
use App\DTOs\Users\UserIndexDTO;
use App\DTOs\Users\UserUpdateDTO;
use App\Http\Requests\Users\UserBanRequest;
use App\Http\Requests\Users\UserDeleteRequest;
use App\Http\Requests\Users\UserIndexRequest;
use App\Http\Requests\Users\UserStoreRequest;
use App\Http\Requests\Users\UserUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\RatingResource;
use App\Http\Resources\UserCommentResource;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserRatingResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSubscriptionResource;
use App\Http\Resources\UserUserListResource;
use App\Http\Resources\UserUserSubscriptionResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * Get paginated list of users with filtering, sorting and pagination
     *
     * @param  UserIndexRequest  $request
     * @param  GetUsers  $action
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function index(UserIndexRequest $request, GetUsers $action): AnonymousResourceCollection
    {
        $dto = UserIndexDTO::fromRequest($request);
        $users = $action->handle($dto);

        return UserResource::collection($users);
    }

    /**
     * Get detailed information about a specific user
     *
     * @param  User  $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user->loadCount(['userLists', 'ratings', 'comments', 'subscriptions']));
    }

    /**
     * Update the specified user
     *
     * @param  UserUpdateRequest  $request
     * @param  User  $user
     * @param  UpdateUser  $action
     * @return UserResource
     * @authenticated
     */
    public function update(UserUpdateRequest $request, User $user, UpdateUser $action): UserResource
    {
        $dto = UserUpdateDTO::fromRequest($request);
        $user = $action->handle($user, $dto);

        return new UserResource($user);
    }

    /**
     * Partially update the specified user
     *
     * @param  UserUpdateRequest  $request
     * @param  User  $user
     * @param  UpdateUser  $action
     * @return UserResource
     * @authenticated
     */
    public function updatePartial(UserUpdateRequest $request, User $user, UpdateUser $action): UserResource
    {
        $dto = UserUpdateDTO::fromRequest($request);
        $user = $action->handle($user, $dto);

        return new UserResource($user);
    }

    /**
     * Store a newly created user
     *
     * @param  UserStoreRequest  $request
     * @param  CreateUser  $action
     * @return UserResource
     * @authenticated
     */
    public function store(UserStoreRequest $request, CreateUser $action): UserResource
    {
        $dto = UserUpdateDTO::fromRequest($request);
        $user = $action->handle($dto);

        return new UserResource($user);
    }

    /**
     * Remove the specified user
     *
     * @param  UserDeleteRequest  $request
     * @param  User  $user
     * @return JsonResponse
     * @authenticated
     */
    public function destroy(UserDeleteRequest $request, User $user): JsonResponse
    {
        // Перевірка, чи користувач намагається видалити себе
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'Cannot delete your own account'
            ], 403);
        }

        // Перевірка, чи користувач намагається видалити адміністратора
        if ($user->isAdmin()) {
            return response()->json([
                'message' => 'Cannot delete an admin user'
            ], 403);
        }

        // Перевірка, чи є у користувача пов'язані дані
        // Тут можна додати перевірки для інших пов'язаних даних, якщо потрібно
        // Наприклад, перевірка наявності коментарів, рейтингів тощо
        if ($user->comments()->exists()) {
            return response()->json([
                'message' => 'Cannot delete user with comments. Delete comments first.'
            ], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Ban the specified user
     *
     * @param  UserBanRequest  $request
     * @param  User  $user
     * @param  UpdateUser  $action
     * @return UserResource
     * @authenticated
     */
    public function ban(UserBanRequest $request, User $user, UpdateUser $action): UserResource|JsonResponse
    {
        // Перевірка, чи користувач намагається заблокувати себе
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'Cannot ban yourself'
            ], 403);
        }

        // Перевірка, чи користувач намагається заблокувати адміністратора
        if ($user->isAdmin()) {
            return response()->json([
                'message' => 'Cannot ban an admin user'
            ], 403);
        }

        $request->merge(['is_banned' => true]);
        $dto = UserUpdateDTO::fromRequest($request);
        $user = $action->handle($user, $dto);

        return new UserResource($user);
    }

    /**
     * Unban the specified user
     *
     * @param  UserBanRequest  $request
     * @param  User  $user
     * @param  UpdateUser  $action
     * @return UserResource
     * @authenticated
     */
    public function unban(UserBanRequest $request, string $id, UpdateUser $action): UserResource
    {
        // Отримуємо користувача без застосування BannedScope
        $user = User::withoutGlobalScope('App\Models\Scopes\BannedScope')->find($id);

        if (!$user) {
            abort(404, 'User not found');
        }

        $request->merge(['is_banned' => false]);
        $dto = UserUpdateDTO::fromRequest($request);
        $user = $action->handle($user, $dto);

        return new UserResource($user);
    }

    /**
     * Get user lists for a specific user
     *
     * @param  User  $user
     * @return AnonymousResourceCollection
     */
    public function userLists(User $user): AnonymousResourceCollection
    {
        $userLists = $user->userLists()->paginate();

        return UserUserListResource::collection($userLists);
    }

    /**
     * Get ratings for a specific user
     *
     * @param  User  $user
     * @return AnonymousResourceCollection
     */
    public function ratings(User $user): AnonymousResourceCollection
    {
        $ratings = $user->ratings()->paginate();

        return UserRatingResource::collection($ratings);
    }

    /**
     * Get comments for a specific user
     *
     * @param  User  $user
     * @return AnonymousResourceCollection
     */
    public function comments(User $user): AnonymousResourceCollection
    {
        $comments = $user->comments()->paginate();

        return UserCommentResource::collection($comments);
    }

    /**
     * Get subscriptions for a specific user
     *
     * @param  User  $user
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function subscriptions(User $user): AnonymousResourceCollection
    {
        $subscriptions = $user->subscriptions()->paginate();

        return UserUserSubscriptionResource::collection($subscriptions);
    }
}
