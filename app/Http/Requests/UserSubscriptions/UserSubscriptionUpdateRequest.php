<?php

namespace App\Http\Requests\UserSubscriptions;

use App\Models\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;

class UserSubscriptionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userSubscription = $this->route('userSubscription');

        return $this->user()->can('update', $userSubscription);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tariff_id' => ['sometimes', 'string', 'exists:tariffs,id'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'is_active' => ['sometimes', 'boolean'],
            'auto_renew' => ['sometimes', 'boolean'],
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
            'tariff_id' => [
                'description' => 'ID тарифу підписки.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'start_date' => [
                'description' => 'Дата початку підписки.',
                'example' => '2023-01-01',
            ],
            'end_date' => [
                'description' => 'Дата закінчення підписки.',
                'example' => '2023-12-31',
            ],
            'is_active' => [
                'description' => 'Чи є підписка активною.',
                'example' => true,
            ],
            'auto_renew' => [
                'description' => 'Чи автоматично продовжувати підписку.',
                'example' => true,
            ],
        ];
    }

    /**
     * Get the URL parameters for the request.
     *
     * @return array
     */
    public function urlParameters()
    {
        return [
            'userSubscription' => [
                'description' => 'ID підписки користувача, яку потрібно оновити.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
        ];
    }
}
