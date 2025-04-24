<?php

namespace App\Http\Requests\Tariffs;

use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TariffUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tariff = $this->route('tariff');
        
        return $this->user()->can('update', $tariff);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tariff = $this->route('tariff');
        
        return [
            'name' => ['sometimes', 'string', 'max:128'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'duration_days' => ['sometimes', 'integer', 'min:1'],
            'features' => ['sometimes', 'array'],
            'features.*' => ['string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'slug' => ['sometimes', 'string', 'max:128', Rule::unique('tariffs', 'slug')->ignore($tariff)],
            'meta_title' => ['nullable', 'string', 'max:128'],
            'meta_description' => ['nullable', 'string', 'max:376'],
            'meta_image' => ['nullable', 'string', 'max:2048', 'url'],
        ];
    }
}
