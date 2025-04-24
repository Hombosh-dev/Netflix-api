<?php

namespace App\Http\Requests\CommentLikes;

use App\Models\CommentLike;
use Illuminate\Foundation\Http\FormRequest;

class CommentLikeDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $commentLike = $this->route('commentLike');
        
        return $this->user()->can('delete', $commentLike);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
