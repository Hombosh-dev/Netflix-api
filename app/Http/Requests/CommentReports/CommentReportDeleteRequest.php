<?php

namespace App\Http\Requests\CommentReports;

use App\Models\CommentReport;
use Illuminate\Foundation\Http\FormRequest;

class CommentReportDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $commentReport = $this->route('commentReport');
        
        return $this->user()->can('delete', $commentReport);
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
