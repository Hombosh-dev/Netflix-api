<?php

namespace App\Http\Requests\Tariffs;

use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;

class TariffDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tariff = $this->route('tariff');
        
        return $this->user()->can('delete', $tariff);
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
}
