<?php

namespace App\Models\Builders;

use App\Enums\UserListType;
use Illuminate\Database\Eloquent\Builder;

class UserListQueryBuilder extends Builder
{
    /**
     * Filter by list type.
     *
     * @param UserListType $type
     * @return self
     */
    public function ofType(UserListType $type): self
    {
        return $this->where('type', $type->value);
    }

    /**
     * Filter by user.
     *
     * @param string $userId
     * @param string|null $listableClass
     * @param UserListType|null $userListType
     * @return self
     */
    public function forUser(
        string $userId,
        ?string $listableClass = null,
        ?UserListType $userListType = null
    ): self {
        return $this->where('user_id', $userId)
            ->when($listableClass, function ($query) use ($listableClass) {
                $query->where('listable_type', $listableClass);
            })
            ->when($userListType, function ($query) use ($userListType) {
                $query->where('type', $userListType->value);
            });
    }

    /**
     * Filter by listable type.
     *
     * @param string $listableType
     * @return self
     */
    public function forListableType(string $listableType): self
    {
        return $this->where('listable_type', $listableType);
    }

    /**
     * Filter by listable.
     *
     * @param string $listableType
     * @param string $listableId
     * @return self
     */
    public function forListable(string $listableType, string $listableId): self
    {
        return $this->where('listable_type', $listableType)
            ->where('listable_id', $listableId);
    }

    /**
     * Get favorites.
     *
     * @return self
     */
    public function favorites(): self
    {
        return $this->ofType(UserListType::FAVORITE);
    }

    /**
     * Get watching.
     *
     * @return self
     */
    public function watching(): self
    {
        return $this->ofType(UserListType::WATCHING);
    }

    /**
     * Get planned.
     *
     * @return self
     */
    public function planned(): self
    {
        return $this->ofType(UserListType::PLANNED);
    }

    /**
     * Get watched.
     *
     * @return self
     */
    public function watched(): self
    {
        return $this->ofType(UserListType::WATCHED);
    }

    /**
     * Get stopped.
     *
     * @return self
     */
    public function stopped(): self
    {
        return $this->ofType(UserListType::STOPPED);
    }

    /**
     * Get rewatching.
     *
     * @return self
     */
    public function rewatching(): self
    {
        return $this->ofType(UserListType::REWATCHING);
    }

    /**
     * Exclude specific list types.
     *
     * @param array<UserListType> $types Array of UserListType values
     * @return self
     */
    public function excludeTypes(array $types): self
    {
        return $this->whereNotIn('type', $types);
    }
}
