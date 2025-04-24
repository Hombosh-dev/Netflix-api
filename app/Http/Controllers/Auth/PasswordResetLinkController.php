<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SendPasswordResetLink;
use App\DTOs\Auth\PasswordResetLinkDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @param Request $request
     * @param SendPasswordResetLink $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request, SendPasswordResetLink $action): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $dto = PasswordResetLinkDTO::fromRequest($request);
        $status = $action->handle($dto);

        return response()->json(['status' => __($status)]);
    }
}
