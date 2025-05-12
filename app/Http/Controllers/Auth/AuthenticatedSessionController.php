<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUser;
// use App\Actions\Auth\LogoutUser; // Не використовуємо для API
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
     * @return JsonResponse
     * @authenticated
     */
    public function destroy(Request $request): JsonResponse
    {
        // Видаляємо лише поточний токен, не використовуючи сесії
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }
}
