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
}
