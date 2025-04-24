<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

class LoginDTO extends BaseDTO
{
    /**
     * Create a new LoginDTO instance.
     *
     * @param string $email
     * @param string $password
     * @param bool $remember
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly bool $remember = false,
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
            'email',
            'password',
            'remember',
        ];
    }
}
