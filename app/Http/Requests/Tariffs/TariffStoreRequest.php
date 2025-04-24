<?php

namespace App\Http\Requests\Tariffs;

use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TariffStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Tariff::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'features' => ['required', 'array'],
            'features.*' => ['string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'slug' => ['required', 'string', 'max:128', 'unique:tariffs,slug'],
            'meta_title' => ['nullable', 'string', 'max:128'],
            'meta_description' => ['nullable', 'string', 'max:376'],
            'meta_image' => ['nullable', 'string', 'max:2048', 'url'],
        ];
    }
}
