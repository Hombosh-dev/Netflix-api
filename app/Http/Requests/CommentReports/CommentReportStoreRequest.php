<?php

namespace App\Http\Requests\CommentReports;

use App\Enums\CommentReportType;
use App\Models\CommentReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CommentReportStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', CommentReport::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comment_id' => ['required', 'string', 'exists:comments,id'],
            'type' => ['required', 'string', new Enum(CommentReportType::class)],
            'body' => ['sometimes', 'nullable', 'string', 'max:1000'],
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
            'comment_id' => [
                'description' => 'ID коментаря, на який подається скарга.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'type' => [
                'description' => 'Тип скарги (SPAM, HARASSMENT, HATE_SPEECH, тощо).',
                'example' => 'SPAM',
            ],
            'body' => [
                'description' => 'Додатковий текст скарги (необов’язково).',
                'example' => 'Цей коментар містить рекламу та не стосується фільму.',
            ],
        ];
    }
}
