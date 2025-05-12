<?php

namespace App\Http\Requests\Selections;

use App\Models\Selection;
use Illuminate\Foundation\Http\FormRequest;

class SelectionDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $selection = $this->route('selection');

        return $this->user()->can('delete', $selection);
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
            'selection' => [
                'description' => 'Slug підбірки, яку потрібно видалити.',
                'example' => 'best-movies-2023-abc123',
            ],
        ];
    }
}
