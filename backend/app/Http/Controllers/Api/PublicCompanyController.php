<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class PublicCompanyController extends Controller
{
    /**
     * GET /api/companies
     * List all verified companies with their open jobs count.
     */
    public function index(): JsonResponse
    {
        $companies = Company::where('is_verified', true)
            ->withCount([
                'jobs as open_jobs_count' => fn ($q) => $q->where('status', 'active')
                    ->where('deadline', '>=', now()->toDateString()),
            ])
            ->orderByDesc('open_jobs_count')
            ->get();

        // Transform logo paths to full URLs
        $companies->each(function ($c) {
            if ($c->logo) {
                $c->logo = asset('storage/' . $c->logo);
            }
        });

        return $this->success('Companies retrieved successfully.', $companies);
    }

    /**
     * GET /api/companies/{slug}
     * Get company profile details with its active job postings.
     */
    public function show(string $slug): JsonResponse
    {
        $company = Company::where('slug', $slug)
            ->where('is_verified', true)
            ->firstOrFail();

        // Transform logo to full URL
        if ($company->logo) {
            $company->logo = asset('storage/' . $company->logo);
        }

        $jobs = $company->jobs()
            ->with(['category:id,name,slug', 'skills:id,name,slug'])
            ->where('status', 'active')
            ->where('deadline', '>=', now()->toDateString())
            ->latest()
            ->get();

        return $this->success('Company details retrieved.', [
            'company' => $company,
            'jobs'    => $jobs,
        ]);
    }
}

