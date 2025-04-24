<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

class PasswordResetLinkDTO extends BaseDTO
{
    /**
     * Create a new PasswordResetLinkDTO instance.
     *
     * @param string $email
     */
    public function __construct(
        public readonly string $email,
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
        ];
    }
}
