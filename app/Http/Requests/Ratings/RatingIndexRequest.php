<?php

namespace App\Http\Requests\Ratings;

use App\Models\Rating;
use Illuminate\Foundation\Http\FormRequest;

class RatingIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Rating::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:number,created_at'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'user_id' => ['sometimes', 'string', 'exists:users,id'],
            'movie_id' => ['sometimes', 'string', 'exists:movies,id'],
            'min_rating' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'max_rating' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'has_review' => ['sometimes', 'boolean'],
        ];
    }
}
