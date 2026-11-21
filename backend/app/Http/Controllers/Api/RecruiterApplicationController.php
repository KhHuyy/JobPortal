<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecruiterApplicationController extends Controller
{
    /**
     * GET /api/recruiter/jobs/{id}/applications
     * List all candidate applications for a specific job, filterable by status.
     */
    public function index(Request $request, int $id): JsonResponse
    {
        $company = $request->user()->company;

        // Verify this job belongs to the recruiter's company
        $job = Job::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $query = Application::with([
            'candidate:id,name,email,phone,avatar',
            'candidate.candidateProfile',
        ])->where('job_id', $job->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->get()->map(function ($app) {
            return [
                'id'             => $app->id,
                'status'         => $app->status,
                'cover_letter'   => $app->cover_letter,
                'recruiter_note' => $app->recruiter_note,
                'cv_file_path'   => asset('storage/' . $app->cv_file_path),
                'created_at'     => $app->created_at,
                'updated_at'     => $app->updated_at,
                'candidate'      => [
                    'id'      => $app->candidate->id,
                    'name'    => $app->candidate->name,
                    'email'   => $app->candidate->email,
                    'phone'   => $app->candidate->phone,
                    'avatar'  => $app->candidate->avatar
                        ? asset('storage/' . $app->candidate->avatar)
                        : null,
                    'profile' => $app->candidate->candidateProfile,
                ],
            ];
        });

        return $this->success('Applications retrieved.', [
            'job'          => [
                'id'    => $job->id,
                'title' => $job->title,
                'slug'  => $job->slug,
            ],
            'applications' => $applications,
        ]);
    }

    /**
     * PATCH /api/recruiter/applications/{id}/status
     * Update application status and optionally save a recruiter note.
     * Allowed statuses: 'reviewed', 'interview', 'offered', 'rejected'.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status'         => 'required|in:reviewed,interview,offered,rejected',
            'recruiter_note' => 'sometimes|nullable|string|max:2000',
        ]);

        $company = $request->user()->company;

        // Find the application, verify it belongs to this recruiter's jobs
        $application = Application::whereHas('job', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->findOrFail($id);

        $application->status         = $validated['status'];
        $application->recruiter_note = $request->recruiter_note ?? $application->recruiter_note;
        $application->save();

        return $this->success('Application status updated.', $application);
    }
}

