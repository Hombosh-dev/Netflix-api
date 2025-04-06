<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'commentable_type' => 'sometimes|required|string',
            'commentable_id'   => 'sometimes|required|string',
            'user_id'          => 'sometimes|required|string',
            'is_spoiler'       => 'nullable|boolean',
            'body'             => 'sometimes|required|string',
            'parent_id'        => 'nullable|string',
        ];
    }
}
