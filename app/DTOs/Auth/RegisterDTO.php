<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

class RegisterDTO extends BaseDTO
{
    /**
     * Create a new RegisterDTO instance.
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $password_confirmation
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $password_confirmation,
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
            'password_confirmation',
        ];
    }
}
