<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class LogoutUser
{
    use AsAction;

    /**
     * Log the user out.
     *
     * @return void
     */
    public function handle(): void
    {
        Auth::guard('web')->logout();

        // Handle session if we're in a web request
        if (request()->hasSession()) {
            // Invalidate the session
            request()->session()->invalidate();

            // Regenerate the CSRF token
            request()->session()->regenerateToken();
        }
    }
}
