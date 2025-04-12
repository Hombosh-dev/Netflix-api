<?php

namespace App\Http\Requests\CommentLike;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $comment_id Ідентифікатор коментаря, який лайкають
 * @property bool $is_liked Чи є це лайком (true) чи дизлайком (false)
 */
class StoreCommentLikeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment_id' => 'required|exists:comments,id', // Перевірка існування коментаря
            'is_liked' => 'required|boolean', // Лайк або дизлайк
        ];
    }

    public function messages(): array
    {
        return [
            'comment_id.required' => 'Необхідно вказати коментар для оцінки.',
            'comment_id.exists' => 'Коментар із таким ID не існує.',
            'is_liked.required' => 'Необхідно вказати, чи це лайк чи дизлайк.',
            'is_liked.boolean' => 'Поле is_liked має бути true або false.',
        ];
    }
}
