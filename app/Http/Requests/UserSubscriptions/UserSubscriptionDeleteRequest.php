<?php

namespace App\Http\Requests\UserSubscriptions;

use App\Models\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;

class UserSubscriptionDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userSubscription = $this->route('userSubscription');
        
        return $this->user()->can('delete', $userSubscription);
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
