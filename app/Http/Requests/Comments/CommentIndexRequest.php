<?php

namespace App\Http\Requests\Comments;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Selection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommentIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Viewing comments is allowed for everyone
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
            'q' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:created_at,likes_count'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'is_spoiler' => ['sometimes', 'boolean'],
            'user_id' => ['sometimes', 'string', 'exists:users,id'],
            'commentable_type' => [
                'sometimes',
                'string',
                Rule::in([
                    Movie::class,
                    Episode::class,
                    Selection::class,
                ])
            ],
            'commentable_id' => ['sometimes', 'string'],
            'is_root' => ['sometimes', 'boolean'],
            'parent_id' => ['sometimes', 'string', 'exists:comments,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // No comma-separated values to convert for now
    }
}
