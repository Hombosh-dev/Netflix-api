<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUser;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param RegisterRequest $request
     * @param RegisterUser $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(RegisterRequest $request, RegisterUser $action): JsonResponse
    {
        $dto = RegisterDTO::fromRequest($request);
        $user = $action->handle($dto);

        // Return 204 No Content as expected by the test
        return response()->json(null, 204);
    }
}
