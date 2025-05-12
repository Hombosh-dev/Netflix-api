<?php

namespace App\Http\Requests\Movies;

use App\Models\Movie;
use Illuminate\Foundation\Http\FormRequest;

class MovieDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $movie = $this->route('movie');

        return $this->user()->can('delete', $movie);
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
            'movie' => [
                'description' => 'Slug фільму, який потрібно видалити.',
                'example' => 'interstellar-abc123',
            ],
        ];
    }
}
