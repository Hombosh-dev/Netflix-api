<?php

namespace App\DTOs\UserLists;

use App\DTOs\BaseDTO;
use App\Enums\UserListType;
use Illuminate\Http\Request;

class UserListStoreDTO extends BaseDTO
{
    /**
     * Create a new UserListStoreDTO instance.
     *
     * @param string $userId User ID
     * @param string $listableType Listable type
     * @param string $listableId Listable ID
     * @param UserListType $type List type
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $listableType,
        public readonly string $listableId,
        public readonly UserListType $type,
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
            'user_id' => 'userId',
            'listable_type' => 'listableType',
            'listable_id' => 'listableId',
            'type',
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
            userId: $request->user()->id,
            listableType: $request->input('listable_type'),
            listableId: $request->input('listable_id'),
            type: UserListType::from($request->input('type')),
        );
    }
}
