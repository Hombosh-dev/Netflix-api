<?php

namespace App\Http\Requests\Episode;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEpisodeRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $episodeId = $this->route('episode')->id ?? null;
        return [
            'movie_id'       => 'sometimes|required|string',
            'number'         => 'sometimes|required|integer',
            'slug'           => "sometimes|required|string|unique:episodes,slug,{$episodeId}|max:255",
            'name'           => 'sometimes|required|string|max:255',
            'description'    => 'nullable|string',
            'duration'       => 'nullable|integer',
            'air_date'       => 'nullable|date_format:Y-m-d',
            'is_filler'      => 'sometimes|required|boolean',
            'pictures'       => 'sometimes|required|string',
            'video_players'  => 'sometimes|required|string',
            'meta_title'     => 'nullable|string|max:255',
            'meta_description'=> 'nullable|string|max:255',
            'meta_image'     => 'nullable|string|max:255',
        ];
    }
}
