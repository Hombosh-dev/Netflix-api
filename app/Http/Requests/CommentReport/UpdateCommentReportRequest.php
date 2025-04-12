<?php

namespace App\Http\Requests\CommentReport;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $description Оновлений опис скарги
 * @property bool|null $is_viewed Статус перегляду
 */
class UpdateCommentReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'description' => 'sometimes|string|max:1000',
            'is_viewed' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'description.max' => 'Опис скарги не може перевищувати 1000 символів.',
            'is_viewed.boolean' => 'Поле is_viewed має бути true або false.',
        ];
    }
}
