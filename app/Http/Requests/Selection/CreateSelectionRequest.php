<?php

namespace App\Http\Requests\Selection;

use Illuminate\Foundation\Http\FormRequest;

class CreateSelectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'           => 'required|string',
            'slug'              => 'required|string|unique:selections,slug|max:255',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:255',
            'meta_image'        => 'nullable|string|max:255',
        ];
    }
}
