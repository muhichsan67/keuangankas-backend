<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            // Admin TIDAK boleh mengubah name (username) dan id
            'email'                 => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'role'                  => ['required', Rule::in(['admin', 'user'])],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'    => 'Email ini sudah digunakan oleh pengguna lain.',
            'role.in'         => 'Role hanya boleh "admin" atau "user".',
            'password.min'    => 'Password minimal 8 karakter.',
        ];
    }
}
