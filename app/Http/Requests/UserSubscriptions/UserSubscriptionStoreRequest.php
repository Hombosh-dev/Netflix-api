<?php

namespace App\Http\Requests\UserSubscriptions;

use App\Models\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;

class UserSubscriptionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', UserSubscription::class);
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
            'tariff_id' => ['required', 'string', 'exists:tariffs,id'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'is_active' => ['sometimes', 'boolean'],
            'auto_renew' => ['sometimes', 'boolean'],
        ];
    }
}
