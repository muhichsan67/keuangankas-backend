<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDebtRequest;
use App\Http\Resources\DebtResource;
use App\Services\DebtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Debts', description: 'Manajemen data hutang pengguna')]
class DebtController extends Controller
{
    public function __construct(protected DebtService $debtService) {}

    #[OA\Get(
        path: '/api/debts',
        summary: 'Daftar semua hutang milik user yang sedang login',
        security: [['sanctum' => []]],
        tags: ['Debts'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar hutang berhasil diambil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/DebtResource')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return DebtResource::collection($this->debtService->getDebtsForUser());
    }

    #[OA\Post(
        path: '/api/debts',
        summary: 'Tambah hutang baru',
        security: [['sanctum' => []]],
        tags: ['Debts'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['source', 'monthly_cost', 'monthly_deadline', 'total_tenor'],
                properties: [
                    new OA\Property(property: 'source', type: 'string', example: 'KPR Bank BCA'),
                    new OA\Property(property: 'monthly_cost', type: 'number', format: 'float', example: 2500000),
                    new OA\Property(property: 'monthly_deadline', type: 'integer', example: 15),
                    new OA\Property(property: 'total_tenor', type: 'integer', example: 120),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Hutang berhasil disimpan'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function store(StoreDebtRequest $request): JsonResponse
    {
        $debt = $this->debtService->createDebt($request->validated());

        return (new DebtResource($debt))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Delete(
        path: '/api/debts/{id}',
        summary: 'Hapus hutang (soft delete)',
        security: [['sanctum' => []]],
        tags: ['Debts'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Hutang berhasil dihapus'),
            new OA\Response(response: 403, description: 'Akses ditolak / IDOR terdeteksi'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->debtService->deleteDebt($id);

        return response()->json(['message' => 'Hutang berhasil dihapus.']);
    }
}
