<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            "title" => "string|max:255",
            "description" => "string|max:255",
            'status' => [
                'string',
                'in:pendiente,en progreso,completada',
            ],
            'priority' => [
                'string',
                'in:alta,media,baja',
            ],
            'users' => [
                'array',
                'exists:users,id',
                'nullable',
            ]
        ];


        if ($this->isMethod('put')) {
            $rules['title'] = 'required|' . $rules['title'];
            $rules['description'] = 'required|' . $rules['description'];
            $rules['status'][] = 'required';
            $rules['priority'][] = 'required';
        }

        return $rules;
    }
}
