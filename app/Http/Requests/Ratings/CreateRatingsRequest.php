<?php

namespace App\Http\Requests\Ratings;

use Illuminate\Foundation\Http\FormRequest;

class CreateRatingsRequest extends FormRequest
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
            'number'   => 'sometimes|required|integer|min:1|max:10',
            'review'   => 'nullable|string',
        ];
    }
}
