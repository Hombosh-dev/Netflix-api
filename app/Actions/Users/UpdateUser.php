<?php

namespace App\Actions\Users;

use App\DTOs\Users\UserUpdateDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUser
{
    use AsAction;

    /**
     * Update a user with the provided data.
     *
     * @param  User  $user
     * @param  UserUpdateDTO  $dto
     * @return User
     */
    public function handle(User $user, UserUpdateDTO $dto): User
    {
        // Update basic information
        if ($dto->name !== null) {
            $user->name = $dto->name;
        }

        if ($dto->email !== null) {
            $user->email = $dto->email;
        }

        if ($dto->password !== null) {
            $user->password = Hash::make($dto->password);
        }

        if ($dto->role !== null) {
            $user->role = $dto->role;
        }

        if ($dto->gender !== null) {
            $user->gender = $dto->gender;
        }

        // Update profile information
        if ($dto->avatar !== null) {
            $user->avatar = $user->handleFileUpload($dto->avatar, 'avatars', $user->avatar);
        }

        if ($dto->backdrop !== null) {
            $user->backdrop = $user->handleFileUpload($dto->backdrop, 'backdrops', $user->backdrop);
        }

        if ($dto->description !== null) {
            $user->description = $dto->description;
        }

        if ($dto->birthday !== null) {
            $user->birthday = $dto->birthday;
        }

        // Update preferences
        if ($dto->allowAdult !== null) {
            $user->allow_adult = $dto->allowAdult;
        }

        if ($dto->isAutoNext !== null) {
            $user->is_auto_next = $dto->isAutoNext;
        }

        if ($dto->isAutoPlay !== null) {
            $user->is_auto_play = $dto->isAutoPlay;
        }

        if ($dto->isAutoSkipIntro !== null) {
            $user->is_auto_skip_intro = $dto->isAutoSkipIntro;
        }

        if ($dto->isPrivateFavorites !== null) {
            $user->is_private_favorites = $dto->isPrivateFavorites;
        }

        // Update moderation status
        if ($dto->isBanned !== null) {
            $user->is_banned = $dto->isBanned;
        }

        $user->save();

        return $user;
    }
}
