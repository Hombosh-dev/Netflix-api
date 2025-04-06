<?php

namespace App\Http\Requests\Episode;

use Illuminate\Foundation\Http\FormRequest;

class CreateEpisodeRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'movie_id'       => 'required|string',
        'number'         => 'required|integer',
        'slug'           => 'required|string|unique:episodes,slug|max:255',
        'name'           => 'required|string|max:255',
        'description'    => 'nullable|string',
        'duration'       => 'nullable|integer',
        'air_date'       => 'nullable|date_format:Y-m-d',
        'is_filler'      => 'required|boolean',
        'pictures'       => 'required|string',
        'video_players'  => 'required|string',
        'meta_title'     => 'nullable|string|max:255',
        'meta_description'=> 'nullable|string|max:255',
        'meta_image'     => 'nullable|string|max:255',
    ];
    }
}
