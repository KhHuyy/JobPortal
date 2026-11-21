<?php

use App\Http\Controllers\Api\AdminCategoryController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminJobController;
use App\Http\Controllers\Api\AdminSkillController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CandidateApplicationController;
use App\Http\Controllers\Api\CandidateProfileController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\PublicCompanyController;
use App\Http\Controllers\Api\PublicJobController;
use App\Http\Controllers\Api\RecruiterApplicationController;
use App\Http\Controllers\Api\RecruiterCompanyController;
use App\Http\Controllers\Api\RecruiterJobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — JobConnect
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically by the framework.
| Authentication: Laravel Sanctum Bearer Token.
| RBAC: Custom RoleMiddleware (role:admin | role:recruiter | role:candidate).
| Response Format: { "success": bool, "message": str, "data": mixed, "errors": mixed }
|
*/

// ============================================================
// PUBLIC ROUTES — No authentication required
// ============================================================

// Authentication
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// Public job listings (active jobs only, paginated, filterable)
Route::get('jobs',        [PublicJobController::class, 'index']);
Route::get('jobs/{slug}', [PublicJobController::class, 'show']);

// Public company directory (verified companies only)
Route::get('companies',        [PublicCompanyController::class, 'index']);
Route::get('companies/{slug}', [PublicCompanyController::class, 'show']);

// Master data: categories, skills, cities for dropdowns
Route::get('master-data', [MasterDataController::class, 'index']);

// ============================================================
// AUTHENTICATED ROUTES — Requires valid Sanctum token
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    // Auth: logout and get current user
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // --------------------------------------------------------
    // CANDIDATE ROUTES — role: candidate
    // --------------------------------------------------------
    Route::prefix('candidate')->middleware('role:candidate')->group(function () {

        // Candidate profile: view and update (with avatar + default CV upload)
        Route::get('profile',  [CandidateProfileController::class, 'show']);
        Route::post('profile', [CandidateProfileController::class, 'update']);

        // Job application: apply, list, toggle-save, saved-jobs
        Route::post('jobs/{id}/apply',       [CandidateApplicationController::class, 'apply']);
        Route::get('applications',           [CandidateApplicationController::class, 'index']);
        Route::post('jobs/{id}/toggle-save', [CandidateApplicationController::class, 'toggleSave']);
        Route::get('saved-jobs',             [CandidateApplicationController::class, 'savedJobs']);
    });

    // --------------------------------------------------------
    // RECRUITER ROUTES — role: recruiter
    // --------------------------------------------------------
    Route::prefix('recruiter')->middleware('role:recruiter')->group(function () {

        // Company profile management (with logo upload)
        Route::get('company',  [RecruiterCompanyController::class, 'show']);
        Route::post('company', [RecruiterCompanyController::class, 'update']);

        // Job posting CRUD
        Route::get('jobs',         [RecruiterJobController::class, 'index']);
        Route::post('jobs',        [RecruiterJobController::class, 'store']);
        Route::put('jobs/{id}',    [RecruiterJobController::class, 'update']);
        Route::delete('jobs/{id}', [RecruiterJobController::class, 'destroy']);

        // ATS: view applicants per job, update application status
        Route::get('jobs/{id}/applications',          [RecruiterApplicationController::class, 'index']);
        Route::patch('applications/{id}/status',      [RecruiterApplicationController::class, 'updateStatus']);

        // Dashboard stats
        Route::get('dashboard-stats', [RecruiterJobController::class, 'dashboardStats']);
    });

    // --------------------------------------------------------
    // ADMIN ROUTES — role: admin
    // --------------------------------------------------------
    Route::prefix('admin')->middleware('role:admin')->group(function () {

        // Platform-wide dashboard statistics
        Route::get('dashboard-stats', [AdminDashboardController::class, 'stats']);

        // Job moderation: list all jobs, approve/reject
        Route::get('jobs',                   [AdminJobController::class, 'index']);
        Route::patch('jobs/{id}/status',     [AdminJobController::class, 'updateStatus']);

        // User management: list all users, ban/unban
        Route::get('users',                  [AdminUserController::class, 'index']);
        Route::patch('users/{id}/toggle-ban', [AdminUserController::class, 'toggleBan']);

        // Category CRUD
        Route::get('categories',          [AdminCategoryController::class, 'index']);
        Route::post('categories',         [AdminCategoryController::class, 'store']);
        Route::put('categories/{id}',     [AdminCategoryController::class, 'update']);
        Route::delete('categories/{id}',  [AdminCategoryController::class, 'destroy']);

        // Skill CRUD
        Route::get('skills',          [AdminSkillController::class, 'index']);
        Route::post('skills',         [AdminSkillController::class, 'store']);
        Route::put('skills/{id}',     [AdminSkillController::class, 'update']);
        Route::delete('skills/{id}',  [AdminSkillController::class, 'destroy']);
    });
});
