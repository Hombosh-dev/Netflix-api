<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUser;
use App\Actions\Auth\LogoutUser;
use App\DTOs\Auth\LoginDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param Request $request
     * @param LoginUser $action
     * @return Response
     * @throws ValidationException
     */
    public function store(Request $request, LoginUser $action): Response
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $dto = LoginDTO::fromRequest($request);
        $action->handle($dto);

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @param LogoutUser $action
     * @return Response
     */
    public function destroy(Request $request, LogoutUser $action): Response
    {
        $action->handle();

        return response()->noContent();
    }
}
