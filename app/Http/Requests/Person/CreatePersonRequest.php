<?php

namespace App\Http\Requests\Person;

use Illuminate\Foundation\Http\FormRequest;

class CreatePersonRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'movie_id'       => 'required|string',
            'person_id'      => 'required|string',
            'voice_person_id'=> 'nullable|string',
            'character_name' => 'required|string|max:255',
        ];
    }
}
