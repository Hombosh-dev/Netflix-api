<?php

namespace App\Http\Requests\MovieNotifications;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieNotificationsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'  => 'sometimes|required|string',
            'movie_id' => 'sometimes|required|string',
        ];
    }
}
