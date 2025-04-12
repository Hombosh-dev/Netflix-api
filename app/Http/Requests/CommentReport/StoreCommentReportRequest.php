<?php

namespace App\Http\Requests\CommentReport;

use App\Enums\CommentReportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $comment_id Ідентифікатор коментаря
 * @property CommentReportType $type Тип скарги
 * @property string|null $description Опис скарги
 */
class StoreCommentReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment_id' => 'required|exists:comments,id',
            'type' => ['required', Rule::enum(CommentReportType::class)],
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'comment_id.required' => 'Необхідно вказати коментар для скарги.',
            'comment_id.exists' => 'Коментар із таким ID не існує.',
            'type.required' => 'Необхідно вказати тип скарги.',
            'type.enum' => 'Невірний тип скарги.',
            'description.max' => 'Опис скарги не може перевищувати 1000 символів.',
        ];
    }
}
