<?php

namespace App\Http\Requests\Movies;

use App\Enums\ApiSourceName;
use App\Enums\AttachmentType;
use App\Enums\Kind;
use App\Enums\MovieRelateType;
use App\Enums\Status;
use App\Rules\FileOrString;
use App\Models\Movie;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class MovieUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $movie = $this->route('movie');

        return $this->user()->can('update', $movie);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $movie = $this->route('movie');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'kind' => ['sometimes', new Enum(Kind::class)],
            'status' => ['sometimes', new Enum(Status::class)],
            'studio_id' => ['nullable', 'string', 'exists:studios,id'],
            'poster' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
            'backdrop' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
            'image_name' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
            'countries' => ['nullable', 'array'],
            'countries.*' => ['string', 'max:2'],
            'aliases' => ['nullable', 'array'],
            'aliases.*' => ['string', 'max:255'],
            'first_air_date' => ['nullable', 'date'],
            'last_air_date' => ['nullable', 'date', 'after_or_equal:first_air_date'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'imdb_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'is_published' => ['sometimes', 'boolean'],
            'attachments' => ['nullable', 'array'],
            'attachments.*.type' => ['required', 'string', new Enum(AttachmentType::class)],
            'attachments.*.url' => ['required', 'string', 'url', 'max:2048'],
            'attachments.*.title' => ['required', 'string', 'max:255'],
            'attachments.*.duration' => ['required', 'integer', 'min:1'],

            'related' => ['nullable', 'array'],
            'related.*.movie_id' => ['required', 'string', 'exists:movies,id'],
            'related.*.type' => ['required', 'string', new Enum(MovieRelateType::class)],

            'similars' => ['nullable', 'array'],
            'similars.*' => ['string', 'exists:movies,id'],

            'api_sources' => ['nullable', 'array'],
            'api_sources.*.source' => ['required', 'string', new Enum(ApiSourceName::class)],
            'api_sources.*.id' => ['required', 'string', 'max:255'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['string', 'exists:tags,id'],
            'person_ids' => ['nullable', 'array'],
            'person_ids.*' => ['exists:people,id'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('movies', 'slug')->ignore($movie)],
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
        $this->convertJsonToArray('countries');
        $this->convertJsonToArray('aliases');
        $this->convertJsonToArray('attachments');
        $this->convertJsonToArray('related');
        $this->convertJsonToArray('similars');
        $this->convertJsonToArray('api_sources');

        // Convert comma-separated values to arrays
        $this->convertCommaSeparatedToArray('tag_ids');
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

    /**
     * Convert comma-separated string to array
     *
     * @param  string  $field
     * @return void
     */
    private function convertCommaSeparatedToArray(string $field): void
    {
        if ($this->has($field) && is_string($this->input($field))) {
            $this->merge([
                $field => explode(',', $this->input($field))
            ]);
        }
    }
}
