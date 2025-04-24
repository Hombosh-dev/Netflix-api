<?php

namespace App\Http\Requests\Tags;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;

class TagDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tag = $this->route('tag');
        
        return $this->user()->can('delete', $tag);
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
