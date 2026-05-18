<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Admin - Trash', description: 'Manajemen data terhapus (khusus admin)')]
class TrashController extends Controller
{
    #[OA\Get(
        path: '/api/admin/trash',
        summary: 'Lihat semua data yang ter-soft delete (admin only)',
        security: [['sanctum' => []]],
        tags: ['Admin - Trash'],
        responses: [
            new OA\Response(response: 200, description: 'Data trash berhasil diambil'),
            new OA\Response(response: 403, description: 'Akses ditolak, bukan admin'),
        ]
    )]
    public function index(): JsonResponse
    {
        $transactions = Transaction::onlyTrashed()->with(['user', 'debt'])->get();
        $debts        = Debt::onlyTrashed()->with('user')->get();

        return response()->json([
            'data' => [
                'transactions' => $transactions,
                'debts'        => $debts,
            ],
        ]);
    }

    #[OA\Post(
        path: '/api/admin/trash/transactions/{id}/restore',
        summary: 'Pulihkan transaksi yang ter-soft delete',
        security: [['sanctum' => []]],
        tags: ['Admin - Trash'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Transaksi berhasil dipulihkan'),
            new OA\Response(response: 403, description: 'Akses ditolak'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function restoreTransaction(int $id): JsonResponse
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id);
        $transaction->restore();

        return response()->json(['message' => 'Transaksi berhasil dipulihkan.']);
    }

    #[OA\Post(
        path: '/api/admin/trash/debts/{id}/restore',
        summary: 'Pulihkan hutang yang ter-soft delete',
        security: [['sanctum' => []]],
        tags: ['Admin - Trash'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Hutang berhasil dipulihkan'),
            new OA\Response(response: 403, description: 'Akses ditolak'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function restoreDebt(int $id): JsonResponse
    {
        $debt = Debt::onlyTrashed()->findOrFail($id);
        $debt->restore();

        return response()->json(['message' => 'Hutang berhasil dipulihkan.']);
    }

    #[OA\Delete(
        path: '/api/admin/trash/transactions/{id}',
        summary: 'Hapus permanen transaksi beserta file kuitansi dari Supabase',
        security: [['sanctum' => []]],
        tags: ['Admin - Trash'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Transaksi berhasil dihapus permanen'),
            new OA\Response(response: 403, description: 'Akses ditolak'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function forceDeleteTransaction(int $id): JsonResponse
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id);

        // Hapus file dari disk aktif (dikontrol RECEIPT_DISK di .env)
        // receipt_url menyimpan relative path: transactions/uuid.jpg
        if ($transaction->receipt_url) {
            Storage::disk(config('filesystems.receipt_disk'))->delete($transaction->receipt_url);
        }

        $transaction->forceDelete();

        return response()->json(['message' => 'Transaksi berhasil dihapus secara permanen.']);
    }

    #[OA\Delete(
        path: '/api/admin/trash/debts/{id}',
        summary: 'Hapus permanen hutang',
        security: [['sanctum' => []]],
        tags: ['Admin - Trash'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Hutang berhasil dihapus permanen'),
            new OA\Response(response: 403, description: 'Akses ditolak'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function forceDeleteDebt(int $id): JsonResponse
    {
        $debt = Debt::onlyTrashed()->findOrFail($id);
        $debt->forceDelete();

        return response()->json(['message' => 'Hutang berhasil dihapus secara permanen.']);
    }
}
