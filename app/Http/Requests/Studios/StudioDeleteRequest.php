<?php

namespace App\Http\Requests\Studios;

use App\Models\Studio;
use Illuminate\Foundation\Http\FormRequest;

class StudioDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $studio = $this->route('studio');

        return $this->user()->can('delete', $studio);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [];
    }

    /**
     * Get the URL parameters for the request.
     *
     * @return array
     */
    public function urlParameters()
    {
        return [
            'studio' => [
                'description' => 'Slug студії, яку потрібно видалити.',
                'example' => 'paramount-pictures-abc123',
            ],
        ];
    }
}
