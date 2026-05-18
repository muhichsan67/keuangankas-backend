<?php

namespace App\Services;

use App\Models\Debt;
use Illuminate\Support\Facades\Auth;

class DebtService
{
    /**
     * Ambil daftar hutang user yang sedang login beserta total cicilan yang telah dibayarkan.
     * Menggunakan withSum() agar agregasi dilakukan di level database (PostgreSQL).
     */
    public function getDebtsForUser(): \Illuminate\Database\Eloquent\Collection
    {
        return Debt::where('user_id', Auth::id())
            ->withSum('expenses as total_paid', 'amount')
            ->latest()
            ->get();
    }

    /**
     * Buat data hutang baru untuk user yang sedang login.
     */
    public function createDebt(array $data): Debt
    {
        return Debt::create([
            ...$data,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Hapus (soft delete) data hutang milik user yang sedang login.
     * Anti-IDOR: scope ke user_id yang terautentikasi.
     */
    public function deleteDebt(int $id): void
    {
        $debt = Debt::where('user_id', Auth::id())->findOrFail($id);
        $debt->delete();
    }
}
