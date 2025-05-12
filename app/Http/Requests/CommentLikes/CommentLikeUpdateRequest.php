<?php

namespace App\Http\Requests\CommentLikes;

use App\Models\CommentLike;
use Illuminate\Foundation\Http\FormRequest;

class CommentLikeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $commentLike = $this->route('commentLike');

        return $this->user()->can('update', $commentLike);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_liked' => ['sometimes', 'boolean'],
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
            'is_liked' => [
                'description' => 'Новий тип реакції (true - лайк, false - дизлайк).',
                'example' => false,
            ],
        ];
    }
}
