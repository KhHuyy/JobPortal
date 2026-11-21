<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PublicJobController extends Controller
{
    /**
     * GET /api/jobs
     * List active jobs with filtering and pagination (10 per page).
     * Filters: keyword, category_id, location, job_type, salary_min, skill_id
     */
    public function index(Request $request): JsonResponse
    {
        $query = Job::with(['company:id,name,slug,logo,city,is_verified', 'category:id,name,slug', 'skills:id,name,slug'])
            ->where('status', 'active')
            ->where('deadline', '>=', Carbon::today());

        // Keyword filter: search title and description
        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('description', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        if ($request->filled('salary_min')) {
            $query->where('salary_min', '>=', $request->salary_min);
        }

        if ($request->filled('skill_id')) {
            $query->whereHas('skills', fn ($q) => $q->where('skills.id', $request->skill_id));
        }

        $jobs = $query->latest()->paginate(10);

        // Transform logo paths to full URLs
        $jobs->through(function ($job) {
            if ($job->company && $job->company->logo) {
                $job->company->logo = asset('storage/' . $job->company->logo);
            }
            return $job;
        });

        return $this->success('Jobs retrieved successfully.', $jobs);
    }

    /**
     * GET /api/jobs/{slug}
     * Get full job details, increment views_count, and return related jobs.
     */
    public function show(string $slug): JsonResponse
    {
        $job = Job::with([
            'company:id,name,slug,logo,city,address,website,company_size,description,is_verified',
            'category:id,name,slug',
            'skills:id,name,slug',
        ])->where('slug', $slug)->firstOrFail();

        // Increment view counter
        $job->increment('views_count');

        // Related jobs: same category, active, excluding current
        $relatedJobs = Job::with(['company:id,name,slug,logo', 'skills:id,name,slug'])
            ->where('status', 'active')
            ->where('deadline', '>=', Carbon::today())
            ->where('category_id', $job->category_id)
            ->where('id', '!=', $job->id)
            ->limit(4)
            ->get();

        // Transform logo paths to full URLs
        if ($job->company && $job->company->logo) {
            $job->company->logo = asset('storage/' . $job->company->logo);
        }
        $relatedJobs->each(function ($rj) {
            if ($rj->company && $rj->company->logo) {
                $rj->company->logo = asset('storage/' . $rj->company->logo);
            }
        });

        return $this->success('Job details retrieved.', [
            'job'          => $job,
            'related_jobs' => $relatedJobs,
        ]);
    }
}

