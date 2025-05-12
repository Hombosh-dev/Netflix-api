<?php

namespace App\Http\Requests\CommentReports;

use App\Enums\CommentReportType;
use App\Models\CommentReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CommentReportIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', CommentReport::class);
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
            'type' => ['sometimes', 'string', new Enum(CommentReportType::class)],
            'is_viewed' => ['sometimes', 'boolean'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:created_at,updated_at'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [];
    }

    /**
     * Get the query parameters for the request.
     *
     * @return array
     */
    public function queryParameters()
    {
        return [
            'comment_id' => [
                'description' => 'ID коментаря для фільтрації скарг.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'user_id' => [
                'description' => 'ID користувача, який подав скарги.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'type' => [
                'description' => 'Тип скарги для фільтрації.',
                'example' => 'SPAM',
            ],
            'is_viewed' => [
                'description' => 'Фільтрувати за статусом перегляду.',
                'example' => false,
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
