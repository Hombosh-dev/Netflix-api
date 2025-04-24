<?php

namespace App\DTOs\UserLists;

use App\DTOs\BaseDTO;
use App\Enums\UserListType;
use Illuminate\Http\Request;

class UserListIndexDTO extends BaseDTO
{
    /**
     * Create a new UserListIndexDTO instance.
     *
     * @param string|null $query Search query
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     * @param string|null $userId Filter by user ID
     * @param array|null $types Filter by list types
     * @param string|null $listableType Filter by listable type
     * @param string|null $listableId Filter by listable ID
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $sort = 'created_at',
        public readonly string $direction = 'desc',
        public readonly ?string $userId = null,
        public readonly ?array $types = null,
        public readonly ?string $listableType = null,
        public readonly ?string $listableId = null,
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
            'user_id' => 'userId',
            'types',
            'listable_type' => 'listableType',
            'listable_id' => 'listableId',
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
        // Process types array
        $types = null;
        if ($request->has('types')) {
            $typesInput = $request->input('types');
            if (is_string($typesInput)) {
                $typesInput = explode(',', $typesInput);
            }
            $types = collect($typesInput)->map(fn($t) => UserListType::from($t))->toArray();
        }

        return new static(
            query: $request->input('q'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
            userId: $request->input('user_id'),
            types: $types,
            listableType: $request->input('listable_type'),
            listableId: $request->input('listable_id'),
        );
    }
}
