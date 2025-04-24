<?php

namespace App\Http\Requests\Persons;

use App\Enums\Gender;
use App\Enums\PersonType;
use App\Rules\FileOrString;
use App\Models\Person;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PersonUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $person = $this->route('person');

        return $this->user()->can('update', $person);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $person = $this->route('person');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', new Enum(PersonType::class)],
            'original_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', new Enum(Gender::class)],
            'image' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
            'description' => ['nullable', 'string'],
            'birthday' => ['nullable', 'date', 'before_or_equal:today'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('people', 'slug')->ignore($person)],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_image' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
        ];
    }
}
