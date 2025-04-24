<?php

namespace App\Actions\UserLists;

use App\DTOs\UserLists\UserListStoreDTO;
use App\Models\UserList;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateUserList
{
    use AsAction;

    /**
     * Create a new user list.
     *
     * @param  UserListStoreDTO  $dto
     * @return UserList
     */
    public function handle(UserListStoreDTO $dto): UserList
    {
        // Check if user list already exists
        $existingList = UserList::forUser($dto->userId)
            ->forListable($dto->listableType, $dto->listableId)
            ->ofType($dto->type)
            ->first();
            
        if ($existingList) {
            return $existingList;
        }
        
        // Create new user list
        $userList = new UserList();
        $userList->user_id = $dto->userId;
        $userList->listable_type = $dto->listableType;
        $userList->listable_id = $dto->listableId;
        $userList->type = $dto->type;
        $userList->save();
        
        return $userList;
    }
}
