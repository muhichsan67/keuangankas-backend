<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            return Category::create([
                'user_id'     => Auth::id(),
                'name'        => $data['name'],
                'type'        => $data['type'],
                'icon'        => $data['icon'],
                'color'       => $data['color'],
            ]);
        });
    }

    public function getCategoriesForUser(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    public function getCategoryById(int $id): Category
    {
        return Category::where('user_id', Auth::id())->findOrFail($id);
    }

    public function getAllCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::select('id', 'name', 'type', 'icon', 'color')->orderBy('name', 'asc')->get();
    }

    public function updateCategory(int $id, array $data): Category
    {
        return DB::transaction(function () use ($id, $data) {
            $category = Category::where('user_id', Auth::id())->findOrFail($id);
            $category->update([
                'name'        => $data['name'],
                'type'        => $data['type'],
                'icon'        => $data['icon'],
                'color'       => $data['color'],
            ]);
            return $category;
        });
    }

    public function deleteCategory(int $id): void
    {
        $category = Category::where('user_id', Auth::id())->findOrFail($id);
        $category->delete();
    }
}
