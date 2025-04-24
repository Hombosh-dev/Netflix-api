<?php

namespace App\Actions\Users;

use App\DTOs\Users\UserIndexDTO;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUsers
{
    use AsAction;

    /**
     * Get paginated list of users with filtering, searching, and sorting.
     *
     * @param  UserIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(UserIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = User::query();

        // Apply search if query is provided
        if ($dto->query) {
            $query->where(function ($q) use ($dto) {
                $q->where('name', 'like', "%{$dto->query}%")
                  ->orWhere('email', 'like', "%{$dto->query}%");
            });
        }

        // Apply filters
        if ($dto->roles) {
            $query->whereIn('role', collect($dto->roles)->map->value->toArray());
        }

        if ($dto->genders) {
            $query->whereIn('gender', collect($dto->genders)->map->value->toArray());
        }

        if ($dto->isBanned !== null) {
            $query->where('is_banned', $dto->isBanned);
        }

        if ($dto->isVerified !== null) {
            if ($dto->isVerified) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Apply date filters
        if ($dto->lastSeenAfter) {
            $query->where('last_seen_at', '>=', Carbon::parse($dto->lastSeenAfter));
        }

        if ($dto->lastSeenBefore) {
            $query->where('last_seen_at', '<=', Carbon::parse($dto->lastSeenBefore));
        }

        if ($dto->createdAfter) {
            $query->where('created_at', '>=', Carbon::parse($dto->createdAfter));
        }

        if ($dto->createdBefore) {
            $query->where('created_at', '<=', Carbon::parse($dto->createdBefore));
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
