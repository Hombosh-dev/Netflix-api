<?php

namespace App\Http\Requests\MovieTag;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieTagRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'movie_id' => 'sometimes|required|string',
            'tag_id'   => 'sometimes|required|string',
        ];
    }
}
