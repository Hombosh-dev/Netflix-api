<?php

namespace App\Http\Requests\Payments;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class PaymentDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $payment = $this->route('payment');
        
        return $this->user()->can('delete', $payment);
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
