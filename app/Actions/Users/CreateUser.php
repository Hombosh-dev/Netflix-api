<?php

namespace App\Actions\Users;

use App\DTOs\Users\UserUpdateDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateUser
{
    use AsAction;

    /**
     * Create a new user with the provided data.
     *
     * @param  UserUpdateDTO  $dto
     * @return User
     */
    public function handle(UserUpdateDTO $dto): User
    {
        $user = new User();

        // Set required fields
        $user->name = $dto->name;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->role = $dto->role;

        // Set optional fields
        if ($dto->gender !== null) {
            $user->gender = $dto->gender;
        }

        if ($dto->avatar !== null) {
            $user->avatar = $user->handleFileUpload($dto->avatar, 'avatars');
        }

        if ($dto->backdrop !== null) {
            $user->backdrop = $user->handleFileUpload($dto->backdrop, 'backdrops');
        }

        if ($dto->description !== null) {
            $user->description = $dto->description;
        }

        if ($dto->birthday !== null) {
            $user->birthday = $dto->birthday;
        }

        // Set preferences
        $user->allow_adult = $dto->allowAdult ?? false;
        $user->is_auto_next = $dto->isAutoNext ?? true;
        $user->is_auto_play = $dto->isAutoPlay ?? true;
        $user->is_auto_skip_intro = $dto->isAutoSkipIntro ?? true;
        $user->is_private_favorites = $dto->isPrivateFavorites ?? false;

        // Set moderation status
        $user->is_banned = $dto->isBanned ?? false;

        $user->save();

        return $user;
    }
}
