<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /**
     * GET /api/admin/categories
     * List all categories.
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('jobs')->orderBy('name')->get();

        return $this->success('Categories retrieved.', $categories);
    }

    /**
     * POST /api/admin/categories
     * Create a new job category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:categories,name',
            'icon'   => 'sometimes|nullable|string|max:100',
            'status' => 'sometimes|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure slug uniqueness
        $slugBase = $validated['slug'];
        $count = 1;
        while (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $slugBase . '-' . $count++;
        }

        $category = Category::create($validated);

        return $this->success('Category created successfully.', $category, 201);
    }

    /**
     * PUT /api/admin/categories/{id}
     * Update a category.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name'   => 'sometimes|string|max:255|unique:categories,name,' . $id,
            'icon'   => 'sometimes|nullable|string|max:100',
            'status' => 'sometimes|boolean',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $category->name) {
            $validated['slug'] = Str::slug($validated['name']);
            $slugBase = $validated['slug'];
            $count = 1;
            while (Category::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $slugBase . '-' . $count++;
            }
        }

        $category->fill($validated)->save();

        return $this->success('Category updated successfully.', $category);
    }

    /**
     * DELETE /api/admin/categories/{id}
     * Delete a category (only if no jobs are using it).
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::withCount('jobs')->findOrFail($id);

        if ($category->jobs_count > 0) {
            return $this->error(
                "Cannot delete category — it has {$category->jobs_count} job(s) assigned to it.",
                null,
                409
            );
        }

        $category->delete();

        return $this->success('Category deleted successfully.');
    }
}

