<?php

namespace App\Http\Requests\Ratings;

use App\Models\Rating;
use Illuminate\Foundation\Http\FormRequest;

class RatingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $rating = $this->route('rating');

        return $this->user()->can('update', $rating);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'review' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'number' => [
                'description' => 'Числовий рейтинг від 1 до 10.',
                'example' => 9,
            ],
            'review' => [
                'description' => 'Текстовий відгук про фільм (необов’язково).',
                'example' => 'Після повторного перегляду я змінив свою думку про цей фільм. Він ще кращий, ніж я думав спочатку.',
            ],
        ];
    }
}
