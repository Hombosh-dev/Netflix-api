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
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSubscriptionResource;
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
     */
    public function destroy(UserDeleteRequest $request, User $user): JsonResponse
    {
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
     */
    public function ban(UserBanRequest $request, User $user, UpdateUser $action): UserResource
    {
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
     */
    public function unban(UserBanRequest $request, User $user, UpdateUser $action): UserResource
    {
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

        return UserListResource::collection($userLists);
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

        return RatingResource::collection($ratings);
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

        return CommentResource::collection($comments);
    }

    /**
     * Get subscriptions for a specific user
     *
     * @param  User  $user
     * @return AnonymousResourceCollection
     */
    public function subscriptions(User $user): AnonymousResourceCollection
    {
        $subscriptions = $user->subscriptions()->paginate();

        return UserSubscriptionResource::collection($subscriptions);
    }
}
