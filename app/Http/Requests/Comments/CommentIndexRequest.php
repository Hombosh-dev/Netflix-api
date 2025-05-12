<?php

namespace App\Http\Requests\Comments;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Selection;
use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array|string>
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
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [];
    }

    /**
     * Get the query parameters for the request.
     *
     * @return array
     */
    public function queryParameters(): array
    {
        return [
            'q' => [
                'description' => 'Пошуковий запит для фільтрації коментарів за текстом.',
                'example' => '',
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
                'description' => 'Поле для сортування результатів (created_at - за датою створення, likes_count - за кількістю вподобань).',
                'example' => 'created_at',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc - за зростанням, desc - за спаданням).',
                'example' => 'desc',
            ],
            'is_spoiler' => [
                'description' => 'Фільтр для відображення тільки коментарів з поміткою про спойлер.',
                'example' => true,
            ],
            'user_id' => [
                'description' => 'Фільтр за ID користувача, який залишив коментар.',
                'example' => '',
            ],
            'commentable_type' => [
                'description' => 'Тип об\'єкта, до якого відноситься коментар (App\\Models\\Movie, App\\Models\\Episode, App\\Models\\Selection).',
                'example' => 'App\\Models\\Movie',
            ],
            'commentable_id' => [
                'description' => 'ID об\'єкта, до якого відноситься коментар.',
                'example' => '',
            ],
            'is_root' => [
                'description' => 'Фільтр для відображення тільки кореневих коментарів (не відповідей).',
                'example' => true,
            ],
            'parent_id' => [
                'description' => 'Фільтр за ID батьківського коментаря (для відображення відповідей).',
                'example' => '',
            ],
        ];
    }
}
