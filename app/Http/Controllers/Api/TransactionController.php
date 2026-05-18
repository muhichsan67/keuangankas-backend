<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\ActivityLogService;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Transactions', description: 'Manajemen transaksi keuangan keluarga')]
class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService,
        protected ActivityLogService $activityLogService,
    ) {}

    #[OA\Get(
        path: '/api/transactions',
        summary: 'Daftar semua transaksi milik user yang sedang login',
        security: [['sanctum' => []]],
        tags: ['Transactions'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar transaksi berhasil diambil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/TransactionResource')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return TransactionResource::collection($this->transactionService->getTransactionsForUser());
    }

    #[OA\Post(
        path: '/api/transactions',
        summary: 'Simpan transaksi baru (mendukung upload kuitansi)',
        security: [['sanctum' => []]],
        tags: ['Transactions'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['type', 'amount', 'category', 'date'],
                    properties: [
                        new OA\Property(property: 'debt_id', type: 'integer', nullable: true, example: 1),
                        new OA\Property(property: 'type', type: 'string', enum: ['in', 'out'], example: 'out'),
                        new OA\Property(property: 'amount', type: 'number', format: 'float', example: 500000),
                        new OA\Property(property: 'category', type: 'string', example: 'Cicilan KPR'),
                        new OA\Property(property: 'date', type: 'string', format: 'date', example: '2025-01-15'),
                        new OA\Property(property: 'description', type: 'string', nullable: true),
                        new OA\Property(property: 'receipt', type: 'string', format: 'binary', nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Transaksi berhasil disimpan'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 403, description: 'Akses ditolak / IDOR terdeteksi'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->createExpense(
            data: $request->validated(),
            receipt: $request->file('receipt'),
        );

        return (new TransactionResource($transaction->load('debt')))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Delete(
        path: '/api/transactions/{id}',
        summary: 'Hapus transaksi (soft delete)',
        security: [['sanctum' => []]],
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Transaksi berhasil dihapus'),
            new OA\Response(response: 403, description: 'Akses ditolak / IDOR terdeteksi'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->transactionService->deleteTransaction($id);

        return response()->json(['message' => 'Transaksi berhasil dihapus.']);
    }
}
