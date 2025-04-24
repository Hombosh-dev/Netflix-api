<?php

namespace App\Http\Requests\CommentLikes;

use App\Models\CommentLike;
use Illuminate\Foundation\Http\FormRequest;

class CommentLikeIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', CommentLike::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comment_id' => ['sometimes', 'string', 'exists:comments,id'],
            'user_id' => ['sometimes', 'string', 'exists:users,id'],
            'is_liked' => ['sometimes', 'boolean'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:created_at'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }
}
