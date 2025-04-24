<?php

namespace App\Http\Requests\UserLists;

use App\Models\UserList;
use Illuminate\Foundation\Http\FormRequest;

class UserListDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userList = $this->route('userList');
        
        return $this->user()->can('delete', $userList);
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
