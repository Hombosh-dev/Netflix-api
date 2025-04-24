<?php

namespace App\Actions\Auth;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class ResendEmailVerification
{
    use AsAction;

    /**
     * Resend the email verification notification.
     *
     * @param User $user
     * @return bool
     */
    public function handle(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->sendEmailVerificationNotification();

        return true;
    }
}
