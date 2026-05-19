<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Contracts\View\View;

class AdminCategoryController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {
        $this->categoryService = new CategoryService();
    }

    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.category.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.category.create');
    }

    public function edit(int $id): View
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    public function store(CreateCategoryRequest $request): RedirectResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        $this->activityLogService->log('CREATE', "Admin membuat kategori baru: {$category->name}");

        return back()->with('success', 'Kategori berhasil dibuat.');
    }

    public function update(UpdateCategoryRequest $request, int $id): RedirectResponse
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());

        $this->activityLogService->log('UPDATE', "Admin mengupdate kategori: {$category->name}");

        return back()->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        $this->activityLogService->log('DELETE', "Admin menghapus kategori: {$category->name}");

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
