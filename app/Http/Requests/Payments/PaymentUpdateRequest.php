<?php

namespace App\Http\Requests\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PaymentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $payment = $this->route('payment');
        
        return $this->user()->can('update', $payment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $payment = $this->route('payment');
        
        return [
            'tariff_id' => ['sometimes', 'string', 'exists:tariffs,id'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'payment_method' => ['sometimes', 'string', 'max:50'],
            'transaction_id' => ['sometimes', 'string', 'max:128', Rule::unique('payments', 'transaction_id')->ignore($payment)],
            'status' => ['sometimes', 'string', new Enum(PaymentStatus::class)],
            'liqpay_data' => ['sometimes', 'array'],
        ];
    }
}
