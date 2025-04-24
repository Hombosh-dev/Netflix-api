<?php

namespace App\DTOs\Users;

use App\DTOs\BaseDTO;
use App\Enums\Gender;
use App\Enums\Role;
use Illuminate\Http\Request;

class UserIndexDTO extends BaseDTO
{
    /**
     * Create a new UserIndexDTO instance.
     *
     * @param string|null $query Search query
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     * @param array|null $roles Filter by roles
     * @param array|null $genders Filter by genders
     * @param bool|null $isBanned Filter by banned status
     * @param bool|null $isVerified Filter by email verification status
     * @param string|null $lastSeenAfter Filter by last seen date
     * @param string|null $lastSeenBefore Filter by last seen date
     * @param string|null $createdAfter Filter by creation date
     * @param string|null $createdBefore Filter by creation date
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $sort = 'created_at',
        public readonly string $direction = 'desc',
        public readonly ?array $roles = null,
        public readonly ?array $genders = null,
        public readonly ?bool $isBanned = null,
        public readonly ?bool $isVerified = null,
        public readonly ?string $lastSeenAfter = null,
        public readonly ?string $lastSeenBefore = null,
        public readonly ?string $createdAfter = null,
        public readonly ?string $createdBefore = null,
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
            'q' => 'query',
            'page',
            'per_page' => 'perPage',
            'sort',
            'direction',
            'roles',
            'genders',
            'is_banned' => 'isBanned',
            'is_verified' => 'isVerified',
            'last_seen_after' => 'lastSeenAfter',
            'last_seen_before' => 'lastSeenBefore',
            'created_after' => 'createdAfter',
            'created_before' => 'createdBefore',
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
        // Process roles array
        $roles = null;
        if ($request->has('roles')) {
            $rolesInput = $request->input('roles');
            if (is_string($rolesInput)) {
                $rolesInput = explode(',', $rolesInput);
            }
            $roles = collect($rolesInput)->map(fn($r) => Role::from($r))->toArray();
        }

        // Process genders array
        $genders = null;
        if ($request->has('genders')) {
            $gendersInput = $request->input('genders');
            if (is_string($gendersInput)) {
                $gendersInput = explode(',', $gendersInput);
            }
            $genders = collect($gendersInput)->map(fn($g) => Gender::from($g))->toArray();
        }

        return new static(
            query: $request->input('q'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
            roles: $roles,
            genders: $genders,
            isBanned: $request->has('is_banned') ? (bool) $request->input('is_banned') : null,
            isVerified: $request->has('is_verified') ? (bool) $request->input('is_verified') : null,
            lastSeenAfter: $request->input('last_seen_after'),
            lastSeenBefore: $request->input('last_seen_before'),
            createdAfter: $request->input('created_after'),
            createdBefore: $request->input('created_before'),
        );
    }
}
