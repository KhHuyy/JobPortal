<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Category;
use App\Models\Company;
use App\Models\Job;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CandidateWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function candidate(): User
    {
        $user = User::factory()->create(['role' => 'candidate', 'status' => 'active']);
        CandidateProfile::create(['user_id' => $user->id]);
        return $user;
    }

    private function activeJob(): Job
    {
        $recruiter = User::factory()->create(['role' => 'recruiter', 'status' => 'active']);
        $company   = Company::factory()->create(['user_id' => $recruiter->id]);
        $category  = Category::factory()->create();
        return Job::factory()->create([
            'company_id'  => $company->id,
            'category_id' => $category->id,
            'status'      => 'active',
            'deadline'    => now()->addDays(30)->toDateString(),
        ]);
    }

    // ── Profile ────────────────────────────────────────────────────────────

    public function test_candidate_can_view_profile(): void
    {
        $user = $this->candidate();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/candidate/profile')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['user', 'profile']])
            ->assertJsonPath('data.user.email', $user->email);
    }

    public function test_candidate_can_update_profile(): void
    {
        $user = $this->candidate();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/candidate/profile', [
                'name'  => 'Updated Name',
                'phone' => '+1 234 567 890',
                'bio'   => 'Professional summary here.',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.user.name', 'Updated Name');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_candidate_can_upload_cv_via_profile(): void
    {
        Storage::fake('public');
        $user = $this->candidate();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/candidate/profile', [
                'name'       => $user->name,
                'default_cv' => UploadedFile::fake()->create('resume.pdf', 500, 'application/pdf'),
            ])
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify a file was stored somewhere in the cvs/ directory
        $files = Storage::disk('public')->files('cvs');
        $this->assertNotEmpty($files, 'Expected CV to be stored under cvs/ directory.');
    }

    public function test_candidate_profile_validation_rejects_non_pdf_cv(): void
    {
        $user = $this->candidate();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/candidate/profile', [
                'default_cv' => UploadedFile::fake()->create('resume.docx', 100, 'application/msword'),
            ])
            ->assertStatus(422);
    }

    // ── Applications ───────────────────────────────────────────────────────

    public function test_candidate_can_apply_to_active_job_with_uploaded_cv(): void
    {
        Storage::fake('public');
        $user = $this->candidate();
        $job  = $this->activeJob();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/candidate/jobs/{$job->id}/apply", [
                'cv_file'      => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
                'cover_letter' => 'I am very interested in this role.',
            ])
            ->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('applications', [
            'job_id'       => $job->id,
            'candidate_id' => $user->id,
            'status'       => 'pending',
        ]);
    }

    public function test_candidate_cannot_apply_twice_to_same_job(): void
    {
        Storage::fake('public');
        $user = $this->candidate();
        $job  = $this->activeJob();

        // First application
        Application::create([
            'job_id'       => $job->id,
            'candidate_id' => $user->id,
            'cv_file_path' => 'cvs/test.pdf',
            'status'       => 'pending',
        ]);

        // Second attempt
        $this->actingAs($user, 'sanctum')
            ->postJson("/api/candidate/jobs/{$job->id}/apply", [
                'cv_file' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
            ])
            ->assertStatus(409);
    }

    public function test_candidate_cannot_apply_without_cv_and_no_default(): void
    {
        $user = $this->candidate();
        $job  = $this->activeJob();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/candidate/jobs/{$job->id}/apply", [
                'cover_letter' => 'No CV provided.',
            ])
            ->assertStatus(422);
    }

    public function test_candidate_can_list_own_applications(): void
    {
        $user = $this->candidate();
        $job  = $this->activeJob();
        Application::create([
            'job_id'       => $job->id,
            'candidate_id' => $user->id,
            'cv_file_path' => 'cvs/test.pdf',
            'status'       => 'pending',
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/candidate/applications')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    // ── Save/Bookmark ──────────────────────────────────────────────────────

    public function test_candidate_can_save_a_job(): void
    {
        $user = $this->candidate();
        $job  = $this->activeJob();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/candidate/jobs/{$job->id}/toggle-save")
            ->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('saved_jobs', [
            'user_id' => $user->id,
            'job_id'  => $job->id,
        ]);
    }

    public function test_candidate_can_unsave_a_job(): void
    {
        $user = $this->candidate();
        $job  = $this->activeJob();
        SavedJob::create(['user_id' => $user->id, 'job_id' => $job->id]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/candidate/jobs/{$job->id}/toggle-save")
            ->assertStatus(200);

        $this->assertDatabaseMissing('saved_jobs', [
            'user_id' => $user->id,
            'job_id'  => $job->id,
        ]);
    }

    public function test_candidate_can_list_saved_jobs(): void
    {
        $user = $this->candidate();
        $job  = $this->activeJob();
        SavedJob::create(['user_id' => $user->id, 'job_id' => $job->id]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/candidate/saved-jobs')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
