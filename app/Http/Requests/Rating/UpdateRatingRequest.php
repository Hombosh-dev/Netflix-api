<?php

namespace App\Http\Requests\Ratings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRatingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'  => 'required|string',
            'movie_id' => 'required|string',
            'number'   => 'required|integer|min:1|max:10',
            'review'   => 'nullable|string',
        ];
    }
}
