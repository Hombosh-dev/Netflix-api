<?php

namespace App\Http\Requests\Ratings;

use App\Models\Rating;
use Illuminate\Foundation\Http\FormRequest;

class RatingDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $rating = $this->route('rating');
        
        return $this->user()->can('delete', $rating);
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
