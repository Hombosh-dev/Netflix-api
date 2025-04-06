<?php

namespace App\Http\Requests\CommentReport;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentReportRequest extends FormRequest
{

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
            'type'       => 'required|string',
            'is_viewed'  => 'sometimes|required|boolean',
        ];
    }
}
