<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterUser
{
    use AsAction;

    /**
     * Register a new user.
     *
     * @param RegisterDTO $dto
     * @return User
     */
    public function handle(RegisterDTO $dto): User
    {
        // Create the user
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        // Dispatch registered event
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        return $user;
    }
}
