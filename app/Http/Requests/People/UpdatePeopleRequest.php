<?php

namespace App\Http\Requests\People;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePeopleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $peopleId = $this->route('people')->id ?? null;
        return [
            'slug'             => "sometimes|required|string|unique:people,slug,{$peopleId}|max:255",
            'name'             => 'sometimes|required|string|max:255',
            'original_name'    => 'nullable|string|max:255',
            'image'            => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'birthday'         => 'nullable|date',
            'birthplace'       => 'nullable|string|max:255',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_image'       => 'nullable|string|max:255',
            'type'             => 'sometimes|required|string',
            'gender'           => 'nullable|string',
        ];
    }
}
