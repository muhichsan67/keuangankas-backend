<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransactionService
{
    /**
     * Buat transaksi baru. Seluruh proses dibungkus dalam DB::transaction().
     * Jika ada file receipt, upload ke Supabase Storage terlebih dahulu.
     */
    public function createExpense(array $data, ?UploadedFile $receipt = null): Transaction
    {
        return DB::transaction(function () use ($data, $receipt) {
            $receiptPath = null;

            if ($receipt !== null) {
                $fileName    = Str::uuid() . '.' . $receipt->getClientOriginalExtension();
                $receiptPath = 'transactions/' . $fileName;

                // Simpan ke disk aktif (ditentukan oleh RECEIPT_DISK di .env)
                Storage::disk(config('filesystems.receipt_disk'))
                    ->put($receiptPath, file_get_contents($receipt->getRealPath()), 'public');
            }

            return Transaction::create([
                'user_id'     => Auth::id(),
                'debt_id'     => $data['debt_id'] ?? null,
                'type'        => $data['type'],
                'amount'      => $data['amount'],
                'category'    => $data['category'],
                'date'        => $data['date'],
                'description' => $data['description'] ?? null,
                'receipt_url' => $receiptPath, // Simpan relative path, bukan full URL
            ]);
        });
    }

    /**
     * Ambil transaksi milik user yang sedang login.
     * Anti-IDOR: scope ke user_id yang terautentikasi.
     */
    public function getTransactionsForUser(): \Illuminate\Database\Eloquent\Collection
    {
        return Transaction::where('user_id', Auth::id())
            ->with('debt')
            ->latest()
            ->get();
    }

    /**
     * Hapus (soft delete) transaksi milik user yang sedang login.
     * Anti-IDOR: scope ke user_id yang terautentikasi.
     */
    public function deleteTransaction(int $id): void
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $transaction->delete();
    }
}
