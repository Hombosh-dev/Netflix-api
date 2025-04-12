<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $content Текст коментаря (опціонально)
 */
class UpdateCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => 'sometimes|string|max:1000',
        ];
    }
}
