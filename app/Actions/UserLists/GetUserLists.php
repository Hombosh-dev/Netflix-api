<?php

namespace App\Actions\UserLists;

use App\DTOs\UserLists\UserListIndexDTO;
use App\Models\UserList;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUserLists
{
    use AsAction;

    /**
     * Get paginated list of user lists with filtering, searching, and sorting.
     *
     * @param  UserListIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(UserListIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = UserList::query()->with(['user']);

        // Apply filters
        if ($dto->userId) {
            $query->forUser($dto->userId);
        }

        if ($dto->types) {
            $query->whereIn('type', collect($dto->types)->map->value->toArray());
        }

        if ($dto->excludeTypes) {
            $query->excludeTypes($dto->excludeTypes);
        }

        if ($dto->listableType) {
            $query->forListableType($dto->listableType);
        }

        if ($dto->listableType && $dto->listableId) {
            $query->forListable($dto->listableType, $dto->listableId);
        }

        // Apply sorting
        $sortField = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';
        $query->orderBy($sortField, $direction);

        // Return paginated results
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
