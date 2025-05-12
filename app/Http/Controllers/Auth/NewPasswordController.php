<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ResetPassword;
use App\DTOs\Auth\PasswordResetDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Auth\NewPasswordRequest;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @param NewPasswordRequest $request
     * @param ResetPassword $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(NewPasswordRequest $request, ResetPassword $action): JsonResponse
    {

        $dto = new PasswordResetDTO(
            email: $request->email,
            password: $request->password,
            token: $request->token
        );

        $status = $action->handle($dto);

        return response()->json(['status' => __($status)]);
    }
}
