<?php

namespace App\Http\Requests\CommentLike;

use Illuminate\Foundation\Http\FormRequest;

class CreateCommentLikeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comment_id' => 'required|string',
            'user_id'    => 'required|string',
            'is_liked'   => 'required|boolean',
        ];
    }
}
