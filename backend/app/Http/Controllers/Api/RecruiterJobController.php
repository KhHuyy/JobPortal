<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RecruiterJobController extends Controller
{
    /**
     * GET /api/recruiter/jobs
     * List all jobs posted by the recruiter's company with applications_count.
     */
    public function index(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        if (! $company) {
            return $this->error('Company profile not found.', null, 404);
        }

        $jobs = Job::where('company_id', $company->id)
            ->withCount('applications')
            ->with(['category:id,name,slug', 'skills:id,name,slug'])
            ->latest()
            ->get();

        return $this->success('Recruiter jobs retrieved.', $jobs);
    }

    /**
     * POST /api/recruiter/jobs
     * Create a new job posting and sync skill_ids in job_skill pivot table.
     */
    public function store(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        if (! $company) {
            return $this->error('Company profile not found. Please set up your company first.', null, 404);
        }

        $validated = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'title'            => 'required|string|max:255',
            'job_type'         => 'required|in:full_time,part_time,remote,internship',
            'experience_level' => 'required|string|max:100',
            'salary_min'       => 'nullable|numeric|min:0',
            'salary_max'       => 'nullable|numeric|min:0|gte:salary_min',
            'is_negotiable'    => 'boolean',
            'location'         => 'required|string|max:100',
            'work_address'     => 'required|string|max:255',
            'vacancies'        => 'integer|min:1',
            'description'      => 'required|string',
            'requirements'     => 'required|string',
            'benefits'         => 'required|string',
            'deadline'         => 'required|date|after:today',
            'skill_ids'        => 'sometimes|array',
            'skill_ids.*'      => 'exists:skills,id',
        ]);

        // Generate a unique slug from title
        $slug = Str::slug($validated['title']);
        $slugBase = $slug;
        $count = 1;
        while (Job::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $count++;
        }

        $job = Job::create([
            'company_id'       => $company->id,
            'category_id'      => $validated['category_id'],
            'title'            => $validated['title'],
            'slug'             => $slug,
            'job_type'         => $validated['job_type'],
            'experience_level' => $validated['experience_level'],
            'salary_min'       => $validated['salary_min'] ?? null,
            'salary_max'       => $validated['salary_max'] ?? null,
            'is_negotiable'    => $validated['is_negotiable'] ?? false,
            'location'         => $validated['location'],
            'work_address'     => $validated['work_address'],
            'vacancies'        => $validated['vacancies'] ?? 1,
            'description'      => $validated['description'],
            'requirements'     => $validated['requirements'],
            'benefits'         => $validated['benefits'],
            'deadline'         => $validated['deadline'],
            'status'           => 'pending', // Jobs start as pending, admin must approve
        ]);

        // Sync skills
        if (! empty($validated['skill_ids'])) {
            $job->skills()->sync($validated['skill_ids']);
        }

        $job->load(['category:id,name,slug', 'skills:id,name,slug']);

        return $this->success('Job posting created successfully. It will be visible after admin approval.', $job, 201);
    }

    /**
     * PUT /api/recruiter/jobs/{id}
     * Update job posting details or change status to 'closed'.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $company = $request->user()->company;

        $job = Job::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $validated = $request->validate([
            'category_id'      => 'sometimes|exists:categories,id',
            'title'            => 'sometimes|string|max:255',
            'job_type'         => 'sometimes|in:full_time,part_time,remote,internship',
            'experience_level' => 'sometimes|string|max:100',
            'salary_min'       => 'sometimes|nullable|numeric|min:0',
            'salary_max'       => 'sometimes|nullable|numeric|min:0',
            'is_negotiable'    => 'sometimes|boolean',
            'location'         => 'sometimes|string|max:100',
            'work_address'     => 'sometimes|string|max:255',
            'vacancies'        => 'sometimes|integer|min:1',
            'description'      => 'sometimes|string',
            'requirements'     => 'sometimes|string',
            'benefits'         => 'sometimes|string',
            'deadline'         => 'sometimes|date',
            'status'           => 'sometimes|in:closed',   // Recruiters can only close, not activate
            'skill_ids'        => 'sometimes|array',
            'skill_ids.*'      => 'exists:skills,id',
        ]);

        // Regenerate slug if title changes
        if (isset($validated['title']) && $validated['title'] !== $job->title) {
            $slug = Str::slug($validated['title']);
            $slugBase = $slug;
            $count = 1;
            while (Job::where('slug', $slug)->where('id', '!=', $job->id)->exists()) {
                $slug = $slugBase . '-' . $count++;
            }
            $validated['slug'] = $slug;
        }

        // Sync skills if provided
        if (isset($validated['skill_ids'])) {
            $job->skills()->sync($validated['skill_ids']);
            unset($validated['skill_ids']);
        }

        $job->fill($validated)->save();
        $job->load(['category:id,name,slug', 'skills:id,name,slug']);

        return $this->success('Job posting updated successfully.', $job);
    }

    /**
     * DELETE /api/recruiter/jobs/{id}
     * Delete a job posting owned by the recruiter.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $company = $request->user()->company;

        $job = Job::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $job->delete();

        return $this->success('Job posting deleted successfully.');
    }

    /**
     * GET /api/recruiter/dashboard-stats
     * Return summary metrics for the recruiter's dashboard.
     */
    public function dashboardStats(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        if (! $company) {
            return $this->success('Dashboard stats retrieved.', [
                'total_jobs'          => 0,
                'active_jobs'         => 0,
                'total_applications'  => 0,
                'pending_applications' => 0,
            ]);
        }

        $jobIds = Job::where('company_id', $company->id)->pluck('id');

        return $this->success('Dashboard stats retrieved.', [
            'total_jobs'           => $jobIds->count(),
            'active_jobs'          => Job::where('company_id', $company->id)->where('status', 'active')->count(),
            'total_applications'   => \App\Models\Application::whereIn('job_id', $jobIds)->count(),
            'pending_applications' => \App\Models\Application::whereIn('job_id', $jobIds)->where('status', 'pending')->count(),
        ]);
    }
}

