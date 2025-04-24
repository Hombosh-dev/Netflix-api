<?php

namespace App\DTOs\Users;

use App\DTOs\BaseDTO;
use App\Enums\Gender;
use App\Enums\Role;
use Illuminate\Http\Request;

class UserUpdateDTO extends BaseDTO
{
    /**
     * Create a new UserUpdateDTO instance.
     *
     * @param string|null $name User name
     * @param string|null $email User email
     * @param string|null $password User password
     * @param Role|null $role User role
     * @param Gender|null $gender User gender
     * @param string|null $avatar User avatar URL
     * @param string|null $backdrop User backdrop URL
     * @param string|null $description User description
     * @param string|null $birthday User birthday
     * @param bool|null $allowAdult Whether user can see adult content
     * @param bool|null $isAutoNext Whether auto next episode is enabled
     * @param bool|null $isAutoPlay Whether auto play is enabled
     * @param bool|null $isAutoSkipIntro Whether auto skip intro is enabled
     * @param bool|null $isPrivateFavorites Whether favorites are private
     * @param bool|null $isBanned Whether user is banned
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?Role $role = null,
        public readonly ?Gender $gender = null,
        public readonly ?string $avatar = null,
        public readonly ?string $backdrop = null,
        public readonly ?string $description = null,
        public readonly ?string $birthday = null,
        public readonly ?bool $allowAdult = null,
        public readonly ?bool $isAutoNext = null,
        public readonly ?bool $isAutoPlay = null,
        public readonly ?bool $isAutoSkipIntro = null,
        public readonly ?bool $isPrivateFavorites = null,
        public readonly ?bool $isBanned = null,
    ) {
    }

    /**
     * Get the fields that should be used for the DTO.
     *
     * @return array
     */
    public static function fields(): array
    {
        return [
            'name',
            'email',
            'password',
            'role',
            'gender',
            'avatar',
            'backdrop',
            'description',
            'birthday',
            'allow_adult' => 'allowAdult',
            'is_auto_next' => 'isAutoNext',
            'is_auto_play' => 'isAutoPlay',
            'is_auto_skip_intro' => 'isAutoSkipIntro',
            'is_private_favorites' => 'isPrivateFavorites',
            'is_banned' => 'isBanned',
        ];
    }

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return new static(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            role: $request->has('role') ? Role::from($request->input('role')) : null,
            gender: $request->has('gender') ? Gender::from($request->input('gender')) : null,
            avatar: $request->input('avatar'),
            backdrop: $request->input('backdrop'),
            description: $request->input('description'),
            birthday: $request->input('birthday'),
            allowAdult: $request->has('allow_adult') ? (bool) $request->input('allow_adult') : null,
            isAutoNext: $request->has('is_auto_next') ? (bool) $request->input('is_auto_next') : null,
            isAutoPlay: $request->has('is_auto_play') ? (bool) $request->input('is_auto_play') : null,
            isAutoSkipIntro: $request->has('is_auto_skip_intro') ? (bool) $request->input('is_auto_skip_intro') : null,
            isPrivateFavorites: $request->has('is_private_favorites') ? (bool) $request->input('is_private_favorites') : null,
            isBanned: $request->has('is_banned') ? (bool) $request->input('is_banned') : null,
        );
    }
}
