<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class MasterDataController extends Controller
{
    /**
     * GET /api/master-data
     * Return all active categories, skills, and predefined cities for dropdown filters.
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon']);

        $skills = Skill::orderBy('name')
            ->get(['id', 'name', 'slug']);

        $cities = [
            'Ho Chi Minh City',
            'Hanoi',
            'Da Nang',
            'Can Tho',
            'Hai Phong',
            'Nha Trang',
            'Hue',
            'Vung Tau',
            'Bien Hoa',
            'Remote',
        ];

        return $this->success('Master data retrieved successfully.', [
            'categories' => $categories,
            'skills'     => $skills,
            'cities'     => $cities,
        ]);
    }
}

