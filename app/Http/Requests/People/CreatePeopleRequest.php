<?php

namespace App\Http\Requests\People;

use Illuminate\Foundation\Http\FormRequest;

class CreatePeopleRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug'             => 'required|string|unique:people,slug|max:255',
            'name'             => 'required|string|max:255',
            'original_name'    => 'nullable|string|max:255',
            'image'            => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'birthday'         => 'nullable|date',
            'birthplace'       => 'nullable|string|max:255',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_image'       => 'nullable|string|max:255',
            'type'             => 'required|string',
            'gender'           => 'nullable|string',
        ];
    }
}
