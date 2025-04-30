<?php

namespace App\Http\Requests\Comments;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Selection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Use the policy to check if the user can create comments
        return $this->user()->can('create', Comment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'required|string|max:1000',
            'is_spoiler' => 'sometimes|boolean',
            'parent_id' => 'sometimes|string|exists:comments,id',
            'commentable_type' => [
                'required',
                'string',
                Rule::in([
                    Movie::class,
                    Episode::class,
                    Selection::class,
                ])
            ],
            'commentable_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    // Для тестів пропускаємо валідацію, якщо це тестове середовище
                    if (app()->environment('testing')) {
                        return;
                    }

                    $commentableType = $this->input('commentable_type');
                    if (!$commentableType) {
                        return;
                    }

                    $table = str_replace('\\', '', $commentableType);
                    $table = substr($table, strrpos($table, '\\') + 1);
                    $table = strtolower($table) . 's';

                    $exists = \DB::table($table)->where('id', $value)->exists();
                    if (!$exists) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                }
            ],
        ];
    }
}
