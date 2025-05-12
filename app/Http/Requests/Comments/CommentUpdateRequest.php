<?php

namespace App\Http\Requests\Comments;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class CommentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $comment = $this->route('comment');

        // Use the policy to check if the user can update the comment
        return $this->user()->can('update', $comment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'required|string|max:1000',
            'is_spoiler' => 'sometimes|boolean',
        ];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'body' => [
                'description' => 'Новий текст коментаря.',
                'example' => 'Оновлений коментар: фільм дійсно чудовий, рекомендую всім!',
            ],
            'is_spoiler' => [
                'description' => 'Чи містить коментар спойлери.',
                'example' => false,
            ],
        ];
    }
}
