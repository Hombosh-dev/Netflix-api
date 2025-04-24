<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\PasswordResetDTO;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetPassword
{
    use AsAction;

    /**
     * Reset the user's password.
     *
     * @param PasswordResetDTO $dto
     * @return string
     * @throws ValidationException
     */
    public function handle(PasswordResetDTO $dto): string
    {
        $status = Password::reset(
            [
                'email' => $dto->email,
                'password' => $dto->password,
                'password_confirmation' => $dto->password,
                'token' => $dto->token,
            ],
            function ($user) use ($dto) {
                $this->updateUserPassword($user, $dto->password);
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $status;
    }

    /**
     * Update the user's password.
     *
     * @param mixed $user
     * @param string $password
     * @return void
     */
    private function updateUserPassword($user, string $password): void
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();
    }
}
