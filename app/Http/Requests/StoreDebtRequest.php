<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDebtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source'           => ['required', 'string', 'max:255'],
            'monthly_cost'     => ['required', 'numeric', 'gt:0'],
            'monthly_deadline' => ['required', 'integer', 'min:1', 'max:31'],
            'total_tenor'      => ['required', 'integer', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'source.required'           => 'Sumber hutang wajib diisi.',
            'monthly_cost.gt'           => 'Biaya cicilan bulanan harus lebih dari 0.',
            'monthly_deadline.min'      => 'Tanggal jatuh tempo minimal 1.',
            'monthly_deadline.max'      => 'Tanggal jatuh tempo maksimal 31.',
            'total_tenor.gt'            => 'Tenor harus lebih dari 0 bulan.',
        ];
    }
}
