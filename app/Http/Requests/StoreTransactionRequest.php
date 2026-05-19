<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'debt_id'     => [
                'nullable',
                'integer',
                // Anti-IDOR: pastikan debt_id milik user yang sedang login
                Rule::exists('debts', 'id')->where(function ($query) {
                    $query->where('user_id', $this->user()->id)
                          ->whereNull('deleted_at');
                }),
            ],
            'type'        => ['required', Rule::in(['in', 'out'])],
            'amount'      => ['required', 'numeric', 'gt:0'],
            'category'    => ['required', 'exists:categories,id'],
            'date'        => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'receipt'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'debt_id.exists'    => 'Hutang tidak ditemukan atau bukan milik Anda.',
            'type.in'           => 'Tipe transaksi harus in atau out.',
            'amount.gt'         => 'Nominal transaksi harus lebih dari 0.',
            'date.required'     => 'Tanggal transaksi wajib diisi.',
            'receipt.image'     => 'File kuitansi harus berupa gambar.',
            'receipt.mimes'     => 'Format kuitansi yang diizinkan: jpeg, png, jpg, webp.',
            'receipt.max'       => 'Ukuran kuitansi maksimal 5MB.',
            'category.exists'   => 'Kategori tidak ditemukan.',
        ];
    }
}
