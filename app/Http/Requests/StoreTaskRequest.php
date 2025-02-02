<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Chequeo authentication
     */
    public function authorize(): bool
    {
        return auth()->check() ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|string|max:255",
            "description" => "required|string|max:255",
            'status' => [
                'required',
                'string',
                'in:pendiente,en progreso,completada',
            ],
            'priority' => [
                'required',
                'string',
                'in:alta,media,baja',
            ],
            'users' => [
                'array',
                'exists:users,id',
                'nullable',
            ]

        ];
    }
}
