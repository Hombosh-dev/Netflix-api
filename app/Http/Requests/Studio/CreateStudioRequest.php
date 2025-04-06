<?php

namespace App\Http\Requests\Studio;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudioRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => 'sometimes|required|string|unique:studios,slug,' . $this->studio->id . '|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_image' => 'nullable|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'aliases' => 'nullable|string|max:255',
            'is_genre' => 'nullable|boolean',
        ];
    }
}
