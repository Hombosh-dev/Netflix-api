<?php

namespace App\Http\Requests\CommentLikes;

use App\Models\CommentLike;
use Illuminate\Foundation\Http\FormRequest;

class CommentLikeStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', CommentLike::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comment_id' => ['required', 'string', 'exists:comments,id'],
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
            'comment_id' => [
                'description' => 'ID коментаря, якому ставиться лайк/дизлайк.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'is_liked' => [
                'description' => 'Тип реакції (true - лайк, false - дизлайк).',
                'example' => true,
            ],
        ];
    }
}
