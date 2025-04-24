<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\PasswordResetLinkDTO;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class SendPasswordResetLink
{
    use AsAction;

    /**
     * Send a password reset link to the user.
     *
     * @param PasswordResetLinkDTO $dto
     * @return string
     * @throws ValidationException
     */
    public function handle(PasswordResetLinkDTO $dto): string
    {
        $status = Password::sendResetLink(['email' => $dto->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $status;
    }
}
