<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            "name" => "string|min:3|max:255",
            "last_name" => "string|min:3|max:255",
            'dni' => 'string|min:8|max:8|unique:users,dni',
            "email" => "email|unique:users,email",
            "password" => "string|min:8|confirmed",
            "roles" => "array|in:admin,user",
        ];

        if ($this->isMethod('patch')) {
            return $rules;
        }

        return array_merge($rules, [
            'dni' => 'required|' . $rules['dni'],
            "email" => 'required|' . $rules['email'],
            "password" => 'required|' . $rules['password'],
            "roles" => 'required|' . $rules['roles'],
        ]);
    }
}
