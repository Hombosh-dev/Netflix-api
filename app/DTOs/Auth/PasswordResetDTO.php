<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

class PasswordResetDTO extends BaseDTO
{
    /**
     * Create a new PasswordResetDTO instance.
     *
     * @param string $email
     * @param string $password
     * @param string $password_confirmation
     * @param string $token
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $password_confirmation,
        public readonly string $token,
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
            'password_confirmation',
            'token',
        ];
    }
}
