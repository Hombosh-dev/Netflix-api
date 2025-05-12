<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ResendEmailVerification;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\EmailVerificationNotificationRequest;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param EmailVerificationNotificationRequest $request
     * @param ResendEmailVerification $action
     * @return JsonResponse|RedirectResponse
     * @authenticated
     */
    public function store(EmailVerificationNotificationRequest $request, ResendEmailVerification $action): JsonResponse|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended('/')
            : $this->sendVerificationEmail($request, $action);
    }

    /**
     * Send verification email to the user.
     *
     * @param EmailVerificationNotificationRequest $request
     * @param ResendEmailVerification $action
     * @return JsonResponse
     */
    private function sendVerificationEmail(EmailVerificationNotificationRequest $request, ResendEmailVerification $action): JsonResponse
    {
        $action->handle($request->user());

        return response()->json(['status' => 'verification-link-sent']);
    }
}
