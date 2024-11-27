<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
            'profile' => ['nullable', 'array'],
            'avatar' => [
                'nullable',
                'image',
                'mimes:' . env('AVATAR_ALLOWED_TYPES', 'jpg,jpeg,png,webp'),
                'max:' . env('AVATAR_MAX_FILE_SIZE', 2048),
            ],
        ];

        // Añadir reglas específicas para crear usuario
        if ($this->isMethod('post')) {
            $rules['email'][] = 'unique:users';
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        // Añadir reglas específicas para actualizar usuario
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['email'][] = 'unique:users,email,' . $this->route('user')->id;
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }
}
