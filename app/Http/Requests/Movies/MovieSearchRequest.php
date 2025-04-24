<?php

namespace App\Http\Requests\Movies;

use App\Enums\Kind;
use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class MovieSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:name,created_at,imdb_score,first_air_date'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'kind' => ['sometimes', new Enum(Kind::class)],
            'status' => ['sometimes', new Enum(Status::class)],
            'min_score' => ['sometimes', 'numeric', 'min:0', 'max:10'],
            'max_score' => ['sometimes', 'numeric', 'min:0', 'max:10'],
            'studio_id' => ['sometimes', 'string', 'exists:studios,id'],
            'tag_id' => ['sometimes', 'string', 'exists:tags,id'],
            'person_id' => ['sometimes', 'string', 'exists:people,id'],
        ];
    }
}
