<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUser;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param Request $request
     * @param RegisterUser $action
     * @return Response
     * @throws ValidationException
     */
    public function store(Request $request, RegisterUser $action): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $dto = RegisterDTO::fromRequest($request);
        $action->handle($dto);

        return response()->noContent();
    }
}
