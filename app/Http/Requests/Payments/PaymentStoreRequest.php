<?php

namespace App\Http\Requests\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PaymentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Payment::class);
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
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'payment_method' => ['required', 'string', 'max:50'],
            'transaction_id' => ['sometimes', 'string', 'max:128', 'unique:payments,transaction_id'],
            'status' => ['sometimes', 'string', new Enum(PaymentStatus::class)],
            'liqpay_data' => ['sometimes', 'array'],
        ];
    }
}
