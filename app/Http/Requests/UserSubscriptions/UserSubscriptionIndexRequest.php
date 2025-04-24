<?php

namespace App\Http\Requests\UserSubscriptions;

use App\Models\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserSubscriptionIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', UserSubscription::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'string', 'exists:users,id'],
            'tariff_id' => ['sometimes', 'string', 'exists:tariffs,id'],
            'is_active' => ['sometimes', 'boolean'],
            'auto_renew' => ['sometimes', 'boolean'],
            'start_date_from' => ['sometimes', 'date'],
            'start_date_to' => ['sometimes', 'date', 'after_or_equal:start_date_from'],
            'end_date_from' => ['sometimes', 'date'],
            'end_date_to' => ['sometimes', 'date', 'after_or_equal:end_date_from'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', Rule::in(['created_at', 'updated_at', 'start_date', 'end_date'])],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
