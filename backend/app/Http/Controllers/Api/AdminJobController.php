<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    /**
     * GET /api/admin/jobs
     * List all jobs across the platform, filterable by status.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Job::with([
            'company:id,name,slug,city,is_verified',
            'category:id,name,slug',
        ])->withCount('applications');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(fn ($q) => $q->where('title', 'like', "%{$kw}%")
                ->orWhereHas('company', fn ($q2) => $q2->where('name', 'like', "%{$kw}%")));
        }

        $jobs = $query->latest()->paginate(15);

        return $this->success('Jobs retrieved.', $jobs);
    }

    /**
     * PATCH /api/admin/jobs/{id}/status
     * Approve (set to 'active') or reject (set to 'rejected') a job posting.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,rejected',
        ]);

        $job = Job::findOrFail($id);
        $job->status = $validated['status'];
        $job->save();

        $action = $validated['status'] === 'active' ? 'approved' : 'rejected';

        return $this->success("Job posting {$action} successfully.", $job);
    }
}

