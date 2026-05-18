<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\Transaction;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class AdminTrashWebController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function index()
    {
        $trashedTransactions = Transaction::onlyTrashed()->with(['user', 'debt'])->latest('deleted_at')->get();
        $trashedDebts        = Debt::onlyTrashed()->with('user')->latest('deleted_at')->get();

        return view('admin.trash.index', compact('trashedTransactions', 'trashedDebts'));
    }

    public function restoreTransaction(int $id): RedirectResponse
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id);
        $transaction->restore();

        $this->activityLogService->log('RESTORE', "Admin memulihkan transaksi ID:{$id} milik user ID:{$transaction->user_id}");

        return back()->with('success', 'Transaksi berhasil dipulihkan.');
    }

    public function restoreDebt(int $id): RedirectResponse
    {
        $debt = Debt::onlyTrashed()->findOrFail($id);
        $debt->restore();

        $this->activityLogService->log('RESTORE', "Admin memulihkan hutang ID:{$id} milik user ID:{$debt->user_id}");

        return back()->with('success', 'Hutang berhasil dipulihkan.');
    }

    public function forceDeleteTransaction(int $id): RedirectResponse
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id);

        // Hapus file dari disk aktif (dikontrol RECEIPT_DISK di .env)
        // receipt_url menyimpan relative path: transactions/uuid.jpg
        if ($transaction->receipt_url) {
            Storage::disk(config('filesystems.receipt_disk'))->delete($transaction->receipt_url);
        }

        $this->activityLogService->log('HARD_DELETE', "Admin menghapus permanen transaksi ID:{$id}");
        $transaction->forceDelete();

        return back()->with('success', 'Transaksi berhasil dihapus secara permanen.');
    }

    public function forceDeleteDebt(int $id): RedirectResponse
    {
        $debt = Debt::onlyTrashed()->findOrFail($id);

        $this->activityLogService->log('HARD_DELETE', "Admin menghapus permanen hutang ID:{$id}");
        $debt->forceDelete();

        return back()->with('success', 'Hutang berhasil dihapus secara permanen.');
    }
}
