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

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [];
    }

    /**
     * Get the URL parameters for the request.
     *
     * @return array
     */
    public function urlParameters()
    {
        return [
            'commentLike' => [
                'description' => 'ID лайку коментаря, який потрібно видалити (ULID).',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
        ];
    }
}
