<?php

namespace App\Http\Requests\Payments;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class PaymentShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $payment = $this->route('payment');
        
        return $this->user()->can('view', $payment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment' => ['required', 'string', 'exists:payments,id'],
        ];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [];
    }

    /**
     * Get the URL parameters for the request.
     *
     * @return array
     */
    public function urlParameters()
    {
        return [
            'payment' => [
                'description' => 'ID платежу, який потрібно отримати (ULID).',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
        ];
    }
}
