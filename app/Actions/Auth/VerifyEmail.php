<?php

namespace App\Actions\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyEmail
{
    use AsAction;

    /**
     * Verify the user's email address.
     *
     * @param EmailVerificationRequest $request
     * @return bool
     */
    public function handle(EmailVerificationRequest $request): bool
    {
        if ($request->user()->hasVerifiedEmail()) {
            return true;
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return true;
    }
}
