<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Categories', description: 'Manajemen kategori keuangan keluarga')]
class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
    ) {}

    #[OA\Get(
        path: '/api/categories',
        summary: 'Daftar semua kategori milik user yang sedang login',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar kategori berhasil diambil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/CategoryResource')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    #[OA\QueryParameter(
        name: 'type',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    public function index(Request $request)
    {
        return CategoryResource::collection($this->categoryService->getCategories($request));
    }

    #[OA\Post(
        path: '/api/categories',
        summary: 'Simpan kategori baru',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['name', 'type'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Gaji'),
                        new OA\Property(property: 'type', type: 'string', enum: ['in', 'out'], example: 'income'),
                        new OA\Property(property: 'icon', type: 'string', example: 'mdi-cash-register'),
                        new OA\Property(property: 'color', type: 'string', example: '#FFD700'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Kategori berhasil disimpan'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 403, description: 'Akses ditolak / IDOR terdeteksi'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function store(StoreCategoryRequest $request): \Illuminate\Http\JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/categories/{id}',
        summary: 'Tampilkan detail kategori',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Kategori berhasil diambil'),
            new OA\Response(response: 404, description: 'Kategori tidak ditemukan'),
            new OA\Response(response: 403, description: 'Akses ditolak / IDOR terdeteksi'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        return (new CategoryResource($category))->response();
    }

    #[OA\Put(
        path: '/api/categories/{id}',
        summary: 'Update kategori yang sudah ada',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['name', 'type'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Gaji'),
                        new OA\Property(property: 'type', type: 'string', enum: ['income', 'expense'], example: 'income'),
                        new OA\Property(property: 'icon', type: 'string', example: 'mdi-cash-register'),
                        new OA\Property(property: 'color', type: 'string', example: '#FFD700'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Kategori berhasil diupdate'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
            new OA\Response(response: 404, description: 'Kategori tidak ditemukan'),
            new OA\Response(response: 403, description: 'Akses ditolak / IDOR terdeteksi'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function update(UpdateCategoryRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());
        return (new CategoryResource($category))->response();
    }

    #[OA\Delete(
        path: '/api/categories/{id}',
        summary: 'Hapus kategori (soft delete)',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Kategori berhasil dihapus'),
            new OA\Response(response: 403, description: 'Akses ditolak / IDOR terdeteksi'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $this->categoryService->deleteCategory($id);
        return response()->json(['message' => 'Category deleted successfully.']);
    }
}
