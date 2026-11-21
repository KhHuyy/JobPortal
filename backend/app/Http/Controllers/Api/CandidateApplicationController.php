<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\SavedJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class CandidateApplicationController extends Controller
{
    /**
     * POST /api/candidate/jobs/{id}/apply
     * Submit a job application.
     * - Prevents duplicate submissions.
     * - Accepts new uploaded .pdf CV or uses candidate's default_cv_path.
     * - Accepts optional cover_letter.
     */
    public function apply(Request $request, int $id): JsonResponse
    {
        $job = Job::where('id', $id)
            ->where('status', 'active')
            ->where('deadline', '>=', Carbon::today())
            ->firstOrFail();

        $candidate = $request->user();

        // Prevent duplicate application
        $existing = Application::where('job_id', $job->id)
            ->where('candidate_id', $candidate->id)
            ->first();

        if ($existing) {
            return $this->error('You have already applied for this job.', null, 409);
        }

        $request->validate([
            'cv_file'      => 'sometimes|file|mimes:pdf|max:5120',
            'cover_letter' => 'sometimes|nullable|string|max:5000',
        ]);

        // Determine CV path: uploaded file takes priority, else use default
        $cvPath = null;

        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('cvs', 'public');
        } else {
            $profile = $candidate->candidateProfile;
            if ($profile && $profile->default_cv_path) {
                $cvPath = $profile->default_cv_path;
            }
        }

        if (! $cvPath) {
            return $this->error(
                'No CV file provided. Please upload a PDF or set a default CV in your profile.',
                null,
                422
            );
        }

        $application = Application::create([
            'job_id'       => $job->id,
            'candidate_id' => $candidate->id,
            'cv_file_path' => $cvPath,
            'cover_letter' => $request->cover_letter,
            'status'       => 'pending',
        ]);

        return $this->success('Application submitted successfully.', $application, 201);
    }

    /**
     * GET /api/candidate/applications
     * List all applications submitted by the current candidate.
     */
    public function index(Request $request): JsonResponse
    {
        $applications = Application::with([
            'job:id,title,slug,job_type,location,salary_min,salary_max,is_negotiable,deadline,status',
            'job.company:id,name,slug,logo',
        ])
            ->where('candidate_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(function ($application) {
                // Transform company logo to full URL
                if ($application->job && $application->job->company && $application->job->company->logo) {
                    $application->job->company->logo = asset('storage/' . $application->job->company->logo);
                }
                return [
                    'id'             => $application->id,
                    'status'         => $application->status,
                    'cover_letter'   => $application->cover_letter,
                    'recruiter_note' => $application->recruiter_note,
                    'cv_file_path'   => asset('storage/' . $application->cv_file_path),
                    'created_at'     => $application->created_at,
                    'updated_at'     => $application->updated_at,
                    'job'            => $application->job,
                ];
            });

        return $this->success('Applications retrieved.', $applications);
    }

    /**
     * POST /api/candidate/jobs/{id}/toggle-save
     * Toggle bookmark (save/unsave) for a job posting.
     */
    public function toggleSave(Request $request, int $id): JsonResponse
    {
        $job = Job::findOrFail($id);
        $userId = $request->user()->id;

        $saved = SavedJob::where('user_id', $userId)
            ->where('job_id', $job->id)
            ->first();

        if ($saved) {
            $saved->delete();
            return $this->success('Job removed from saved list.');
        }

        SavedJob::create([
            'user_id'    => $userId,
            'job_id'     => $job->id,
            'created_at' => now(),
        ]);

        return $this->success('Job saved to your bookmarks.', null, 201);
    }

    /**
     * GET /api/candidate/saved-jobs
     * List all bookmarked jobs for the current candidate.
     */
    public function savedJobs(Request $request): JsonResponse
    {
        $savedJobs = SavedJob::with([
            'job:id,title,slug,job_type,location,salary_min,salary_max,is_negotiable,deadline,status',
            'job.company:id,name,slug,logo,city',
            'job.skills:id,name,slug',
        ])
            ->where('user_id', $request->user()->id)
            ->latest('created_at')
            ->get()
            ->map(function ($saved) {
                $job = $saved->job;
                // Transform company logo to full URL
                if ($job && $job->company && $job->company->logo) {
                    $job->company->logo = asset('storage/' . $job->company->logo);
                }
                return $job;
            });

        return $this->success('Saved jobs retrieved.', $savedJobs);
    }
}

