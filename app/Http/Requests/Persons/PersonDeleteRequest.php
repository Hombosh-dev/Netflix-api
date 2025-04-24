<?php

namespace App\Http\Requests\Persons;

use App\Models\Person;
use Illuminate\Foundation\Http\FormRequest;

class PersonDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $person = $this->route('person');
        
        return $this->user()->can('delete', $person);
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
