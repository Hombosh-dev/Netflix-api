<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SendPasswordResetLink;
use App\DTOs\Auth\PasswordResetLinkDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Auth\PasswordResetLinkRequest;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @param PasswordResetLinkRequest $request
     * @param SendPasswordResetLink $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(PasswordResetLinkRequest $request, SendPasswordResetLink $action): JsonResponse
    {

        $dto = PasswordResetLinkDTO::fromRequest($request);
        $status = $action->handle($dto);

        return response()->json(['status' => __($status)]);
    }
}
