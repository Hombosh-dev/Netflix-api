<?php

namespace App\Http\Requests\CommentReports;

use App\Enums\CommentReportType;
use App\Models\CommentReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CommentReportUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $commentReport = $this->route('commentReport');

        return $this->user()->can('update', $commentReport);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_viewed' => ['sometimes', 'boolean'],
            'type' => ['sometimes', 'string', new Enum(CommentReportType::class)],
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
            'is_viewed' => [
                'description' => 'Статус перегляду скарги.',
                'example' => true,
            ],
            'type' => [
                'description' => 'Тип скарги (SPAM, HARASSMENT, HATE_SPEECH, тощо).',
                'example' => 'HATE_SPEECH',
            ],
            'body' => [
                'description' => 'Додатковий текст скарги (необов’язково).',
                'example' => 'Після перевірки виявлено, що це не спам, а мова ненависті.',
            ],
        ];
    }
}
