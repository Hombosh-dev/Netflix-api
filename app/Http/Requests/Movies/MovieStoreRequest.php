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
use Illuminate\Validation\Rules\Enum;

class MovieStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Movie::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'kind' => ['required', new Enum(Kind::class)],
            'status' => ['required', new Enum(Status::class)],
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
            'slug' => ['nullable', 'string', 'max:255', 'unique:movies,slug'],
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

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'Назва фільму або серіалу.',
                'example' => 'Інтерстеллар',
            ],
            'description' => [
                'description' => 'Опис фільму або серіалу.',
                'example' => 'Фільм про подорож групи дослідників у космосі...',
            ],
            'kind' => [
                'description' => 'Тип контенту (MOVIE - фільм, TV_SERIES - серіал, тощо).',
                'example' => 'MOVIE',
            ],
            'status' => [
                'description' => 'Статус контенту (RELEASED - випущено, IN_PRODUCTION - у виробництві, тощо).',
                'example' => 'RELEASED',
            ],
            'studio_id' => [
                'description' => 'ID студії, яка створила фільм (необов\'язково).',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'poster' => [
                'description' => 'Постер фільму (файл або URL, необов\'язково).',
                'example' => 'https://example.com/poster.jpg',
            ],
            'backdrop' => [
                'description' => 'Фонове зображення фільму (файл або URL, необов\'язково).',
                'example' => 'https://example.com/backdrop.jpg',
            ],
            'image_name' => [
                'description' => 'Зображення з назвою фільму (файл або URL, необов\'язково).',
                'example' => 'https://example.com/title.jpg',
            ],
            'countries' => [
                'description' => 'Масив кодів країн виробництва (формат ISO 3166-1 alpha-2).',
                'example' => ['US', 'GB'],
            ],
            'aliases' => [
                'description' => 'Масив альтернативних назв фільму.',
                'example' => ['Зоряні війни', 'Star Wars'],
            ],
            'first_air_date' => [
                'description' => 'Дата першого виходу в ефір.',
                'example' => '2014-11-07',
            ],
            'last_air_date' => [
                'description' => 'Дата останнього виходу в ефір (для серіалів).',
                'example' => '2014-11-07',
            ],
            'duration' => [
                'description' => 'Тривалість фільму в хвилинах.',
                'example' => 169,
            ],
            'imdb_score' => [
                'description' => 'Рейтинг IMDb від 0 до 10.',
                'example' => 8.6,
            ],
            'is_published' => [
                'description' => 'Чи опублікований фільм на сайті.',
                'example' => true,
            ],
            'attachments' => [
                'description' => 'Масив відеоплеєрів та трейлерів.',
                'example' => [
                    [
                        'type' => 'TRAILER',
                        'url' => 'https://www.youtube.com/watch?v=zSWdZVtXT7E',
                        'title' => 'Офіційний трейлер',
                        'duration' => 145,
                    ],
                ],
            ],
            'related' => [
                'description' => 'Масив пов\'язаних фільмів з вказанням типу зв\'язку.',
                'example' => [
                    [
                        'movie_id' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
                        'type' => 'SEQUEL',
                    ],
                ],
            ],
            'similars' => [
                'description' => 'Масив ID подібних фільмів.',
                'example' => ['01HN5PXMEH6SDMF0KAVSW1DYTY', '01HN5PXMEH6SDMF0KAVSW1DYTZ'],
            ],
            'api_sources' => [
                'description' => 'Масив джерел даних з API.',
                'example' => [
                    [
                        'source' => 'TMDB',
                        'id' => '157336',
                    ],
                ],
            ],
            'tag_ids' => [
                'description' => 'Масив ID тегів.',
                'example' => ['01HN5PXMEH6SDMF0KAVSW1DYTY', '01HN5PXMEH6SDMF0KAVSW1DYTZ'],
            ],
            'person_ids' => [
                'description' => 'Масив ID персон, пов\'язаних з фільмом.',
                'example' => ['01HN5PXMEH6SDMF0KAVSW1DYTY', '01HN5PXMEH6SDMF0KAVSW1DYTZ'],
            ],
            'slug' => [
                'description' => 'Унікальний ідентифікатор для URL.',
                'example' => 'interstellar',
            ],
            'meta_title' => [
                'description' => 'SEO заголовок.',
                'example' => 'Інтерстеллар (2014) - Дивитись онлайн',
            ],
            'meta_description' => [
                'description' => 'SEO опис.',
                'example' => 'Дивіться Інтерстеллар (2014) онлайн безкоштовно в HD якості.',
            ],
            'meta_image' => [
                'description' => 'SEO зображення (файл або URL).',
                'example' => 'https://example.com/meta-image.jpg',
            ],
        ];
    }
}
