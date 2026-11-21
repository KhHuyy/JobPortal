<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    /**
     * GET /api/admin/dashboard-stats
     * Return platform-wide metrics for the admin dashboard.
     */
    public function stats(): JsonResponse
    {
        return $this->success('Dashboard stats retrieved.', [
            'total_candidates'   => User::where('role', 'candidate')->count(),
            'total_recruiters'   => User::where('role', 'recruiter')->count(),
            'total_jobs'         => Job::count(),
            'active_jobs'        => Job::where('status', 'active')->count(),
            'pending_jobs'       => Job::where('status', 'pending')->count(),
            'total_applications' => Application::count(),
            'total_users'        => User::count(),
        ]);
    }
}

