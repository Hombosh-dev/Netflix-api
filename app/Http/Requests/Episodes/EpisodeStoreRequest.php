<?php

namespace App\Http\Requests\Episodes;

use App\Enums\VideoPlayerName;
use App\Enums\VideoQuality;
use App\Models\Episode;
use App\Rules\FileOrString;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class EpisodeStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Episode::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'movie_id' => ['required', 'string', 'exists:movies,id'],
            'number' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'air_date' => ['nullable', 'date'],
            'is_filler' => ['sometimes', 'boolean'],
            'pictures' => ['nullable', 'array'],
            'pictures.*' => [new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
            'video_players' => ['nullable', 'array'],
            'video_players.*.name' => ['required', 'string', new Enum(VideoPlayerName::class)],
            'video_players.*.url' => ['required', 'string', 'max:2048'],
            'video_players.*.file_url' => ['nullable', 'string', 'max:2048'],
            'video_players.*.dubbing' => ['nullable', 'string', 'max:50'],
            'video_players.*.quality' => ['required', 'string', new Enum(VideoQuality::class)],
            'video_players.*.locale_code' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:episodes,slug'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_image' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Convert JSON strings to arrays
        $this->convertJsonToArray('pictures');
        $this->convertJsonToArray('video_players');
    }

    /**
     * Convert JSON string to array
     *
     * @param  string  $field
     * @return void
     */
    private function convertJsonToArray(string $field): void
    {
        if ($this->has($field) && is_string($this->input($field))) {
            $this->merge([
                $field => json_decode($this->input($field), true) ?? []
            ]);
        }
    }
}
