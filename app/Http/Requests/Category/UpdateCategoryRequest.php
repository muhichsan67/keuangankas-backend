<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()->isAdmin();
    }

    public function rules(): array {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'type'                  => ['required', Rule::in(['income', 'expense'])],
            'icon'                  => ['nullable', 'string', 'max:255'],
            'color'                 => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array {
        return [
            'name.required'     => 'Nama wajib diisi.',
            'type.in'           => 'Jenis harus income atau expense.',
        ];
    }
}
