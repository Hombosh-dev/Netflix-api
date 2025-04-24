<?php

namespace App\Http\Requests\UserLists;

use App\Enums\UserListType;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Tag;
use App\Models\UserList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UserListStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', UserList::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(UserListType::class)],
            'listable_type' => [
                'required', 
                'string', 
                Rule::in([
                    Movie::class,
                    Episode::class,
                    Person::class,
                    Tag::class,
                    Selection::class,
                ])
            ],
            'listable_id' => [
                'required', 
                'string', 
                function ($attribute, $value, $fail) {
                    $listableType = $this->input('listable_type');
                    if (!$listableType) {
                        return;
                    }
                    
                    $table = str_replace('\\', '', $listableType);
                    $table = substr($table, strrpos($table, '\\') + 1);
                    $table = strtolower($table) . 's';
                    
                    $exists = \DB::table($table)->where('id', $value)->exists();
                    if (!$exists) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                }
            ],
        ];
    }
}
