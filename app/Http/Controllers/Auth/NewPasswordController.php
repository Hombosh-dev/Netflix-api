<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ResetPassword;
use App\DTOs\Auth\PasswordResetDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @param Request $request
     * @param ResetPassword $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request, ResetPassword $action): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $dto = new PasswordResetDTO(
            email: $request->email,
            password: $request->password,
            token: $request->token
        );

        $status = $action->handle($dto);

        return response()->json(['status' => __($status)]);
    }
}
