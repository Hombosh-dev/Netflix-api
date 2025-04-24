<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\VerifyEmail;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param EmailVerificationRequest $request
     * @param VerifyEmail $action
     * @return RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request, VerifyEmail $action): RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(config('app.frontend_url').'/?verified=1')
            : $this->verifyEmail($request, $action);
    }

    /**
     * Verify the user's email address.
     *
     * @param EmailVerificationRequest $request
     * @param VerifyEmail $action
     * @return RedirectResponse
     */
    private function verifyEmail(EmailVerificationRequest $request, VerifyEmail $action): RedirectResponse
    {
        $action->handle($request);

        return redirect()->intended(config('app.frontend_url').'/?verified=1');
    }
}
