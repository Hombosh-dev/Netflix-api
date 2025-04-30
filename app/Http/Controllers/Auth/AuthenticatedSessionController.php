<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUser;
use App\Actions\Auth\LogoutUser;
use App\DTOs\Auth\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param Request $request
     * @param LoginUser $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request, LoginUser $action): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $dto = LoginDTO::fromRequest($request);
        $action->handle($dto);

        // Get the authenticated user
        $user = Auth::user();

        // Видаляємо старі токени користувача (опціонально)
        // $user->tokens()->delete();

        // Створюємо новий токен через Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @param LogoutUser $action
     * @return JsonResponse
     */
    public function destroy(Request $request, LogoutUser $action): JsonResponse
    {
        // Revoke the token that was used to authenticate the current request
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        $action->handle();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
