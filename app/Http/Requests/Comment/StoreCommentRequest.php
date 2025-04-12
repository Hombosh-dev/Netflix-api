<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $content Текст коментаря
 * @property int $user_id Ідентифікатор користувача
 * @property int|null $parent_id Ідентифікатор батьківського коментаря (опціонально)
 * @property int $commentable_id Ідентифікатор пов’язаного об’єкта
 * @property string $commentable_type Тип пов’язаного об’єкта (поліморфний)
 */
class StoreCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'user_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:comments,id',
            'commentable_id' => 'required|exists:commentables,id',
            'commentable_type' => 'required|string',
        ];
    }
}
