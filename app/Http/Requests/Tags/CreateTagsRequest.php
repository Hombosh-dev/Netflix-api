<?php

namespace App\Http\Requests\Tags;

use Illuminate\Foundation\Http\FormRequest;

class CreateTagsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug'             => 'required|string|unique:tags,slug|max:255',
            'name'             => 'required|string|max:255',
            'description'      => 'required|string',
            'image'            => 'nullable|string|max:255',
            'aliases'          => 'nullable|array',
            'is_genre'         => 'required|boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_image'       => 'nullable|string|max:255',
        ];
    }
}
