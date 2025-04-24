<?php

namespace App\Http\Requests\Episodes;

use App\Models\Episode;
use Illuminate\Foundation\Http\FormRequest;

class EpisodeDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $episode = $this->route('episode');
        
        return $this->user()->can('delete', $episode);
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
