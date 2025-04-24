<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Exceptions\RateLimitExceededException;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class LoginUser
{
    use AsAction;

    /**
     * Maximum number of login attempts.
     *
     * @var int
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Authenticate a user.
     *
     * @param  LoginDTO  $dto
     * @return bool
     * @throws ValidationException
     * @throws RateLimitExceededException
     */
    public function handle(LoginDTO $dto): bool
    {
        $this->ensureNotRateLimited($dto->email);

        $credentials = [
            'email' => $dto->email,
            'password' => $dto->password,
        ];

        if (!Auth::attempt($credentials, $dto->remember)) {
            RateLimiter::hit($this->throttleKey($dto->email));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($dto->email));

        // Regenerate the session if we're in a web request
        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return true;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @param  string  $email
     * @return void
     * @throws RateLimitExceededException
     */
    private function ensureNotRateLimited(string $email): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($email), self::MAX_ATTEMPTS)) {
            $this->handleRateLimiting($email);
        }
    }

    /**
     * Handle rate limiting and throw appropriate exception.
     *
     * @param  string  $email
     * @return void
     */
    private function handleRateLimiting(string $email): void
    {
        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey($email));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key.
     *
     * @param  string  $email
     * @return string
     */
    private function throttleKey(string $email): string
    {
        return Str::transliterate(Str::lower($email).'|'.request()->ip());
    }
}
