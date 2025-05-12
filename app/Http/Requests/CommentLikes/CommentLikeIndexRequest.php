<?php

namespace App\Http\Requests\CommentLikes;

use App\Models\CommentLike;
use Illuminate\Foundation\Http\FormRequest;

class CommentLikeIndexRequest extends FormRequest
{
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

    /**
     * Get the query parameters for the request.
     *
     * @return array
     */
    public function queryParameters(): array
    {
        return [
            'comment_id' => [
                'description' => 'ID коментаря для фільтрації лайків.',
                'example' => '',
            ],
            'user_id' => [
                'description' => 'ID користувача для фільтрації лайків.',
                'example' => '',
            ],
            'is_liked' => [
                'description' => 'Фільтрувати за типом лайка (true - лайк, false - дизлайк).',
                'example' => true,
            ],
            'page' => [
                'description' => 'Номер сторінки для пагінації.',
                'example' => 1,
            ],
            'per_page' => [
                'description' => 'Кількість елементів на сторінці.',
                'example' => 15,
            ],
            'sort' => [
                'description' => 'Поле для сортування результатів.',
                'example' => 'created_at',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc або desc).',
                'example' => 'desc',
            ],
        ];
    }
}
