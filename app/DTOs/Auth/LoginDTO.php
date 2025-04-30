<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

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

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return new static(
            email: $request->input('email'),
            password: $request->input('password'),
            remember: (bool) $request->input('remember', false),
        );
    }
}
