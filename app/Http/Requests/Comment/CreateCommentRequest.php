<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class CreateCommentRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'commentable_type' => 'required|string',
            'commentable_id'   => 'required|string',
            'user_id'          => 'required|string',
            'is_spoiler'       => 'nullable|boolean',
            'body'             => 'required|string',
            'parent_id'        => 'nullable|string',
        ];
    }
}
