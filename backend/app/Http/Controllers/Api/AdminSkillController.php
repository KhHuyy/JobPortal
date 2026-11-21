<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSkillController extends Controller
{
    /**
     * GET /api/admin/skills
     * List all skills.
     */
    public function index(): JsonResponse
    {
        $skills = Skill::withCount('jobs')->orderBy('name')->get();

        return $this->success('Skills retrieved.', $skills);
    }

    /**
     * POST /api/admin/skills
     * Create a new skill tag.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:skills,name',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure slug uniqueness
        $slugBase = $validated['slug'];
        $count = 1;
        while (Skill::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $slugBase . '-' . $count++;
        }

        $skill = Skill::create($validated);

        return $this->success('Skill created successfully.', $skill, 201);
    }

    /**
     * PUT /api/admin/skills/{id}
     * Update a skill.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $skill = Skill::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:skills,name,' . $id,
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure slug uniqueness excluding current
        $slugBase = $validated['slug'];
        $count = 1;
        while (Skill::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
            $validated['slug'] = $slugBase . '-' . $count++;
        }

        $skill->fill($validated)->save();

        return $this->success('Skill updated successfully.', $skill);
    }

    /**
     * DELETE /api/admin/skills/{id}
     * Delete a skill (detaches from all jobs automatically via cascade).
     */
    public function destroy(int $id): JsonResponse
    {
        $skill = Skill::findOrFail($id);
        $skill->delete();

        return $this->success('Skill deleted successfully.');
    }
}

