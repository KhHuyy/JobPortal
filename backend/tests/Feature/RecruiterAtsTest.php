<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecruiterAtsTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ helpers

    private function recruiterWithCompany(): array
    {
        $user    = User::factory()->create(['role' => 'recruiter', 'status' => 'active']);
        $company = Company::factory()->create(['user_id' => $user->id]);
        return [$user, $company];
    }

    private function jobPayload(int $categoryId): array
    {
        return [
            'title'            => 'Test Engineer',
            'category_id'     => $categoryId,
            'job_type'        => 'full_time',
            'experience_level' => 'Entry Level',
            'location'        => 'Ho Chi Minh City',
            'work_address'    => '123 Main St',
            'description'     => 'Full description here',
            'requirements'    => 'Requirements here',
            'benefits'        => 'Health insurance',
            'deadline'        => now()->addDays(30)->format('Y-m-d'),
            'vacancies'       => 1,
        ];
    }

    // ------------------------------------------------------------------ tests

    public function test_recruiter_can_view_own_company(): void
    {
        [$user, $company] = $this->recruiterWithCompany();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/recruiter/company');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_recruiter_can_update_company(): void
    {
        [$user, $company] = $this->recruiterWithCompany();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/recruiter/company', [
                'name'         => 'Updated Corp',
                'company_size' => '11-50',
                'description'  => 'Updated description',
                'address'      => '456 Business Ave',
                'city'         => 'Ha Noi',
                'website'      => 'https://updated.example.com',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_recruiter_can_create_job(): void
    {
        [$user, $company] = $this->recruiterWithCompany();
        $category = Category::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/recruiter/jobs', $this->jobPayload($category->id));

        $response->assertStatus(201)
            ->assertJsonPath('success', true);
    }

    public function test_recruiter_can_update_job(): void
    {
        [$user, $company] = $this->recruiterWithCompany();
        $category = Category::factory()->create();
        $job = Job::factory()->create([
            'company_id'  => $company->id,
            'category_id' => $category->id,
            'status'      => 'pending',
        ]);

        $payload = $this->jobPayload($category->id);
        $payload['title'] = 'Updated Engineer';

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/recruiter/jobs/{$job->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_recruiter_can_delete_job(): void
    {
        [$user, $company] = $this->recruiterWithCompany();
        $category = Category::factory()->create();
        $job = Job::factory()->create([
            'company_id'  => $company->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/recruiter/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_recruiter_can_view_applicants(): void
    {
        [$user, $company] = $this->recruiterWithCompany();
        $category = Category::factory()->create();
        $job = Job::factory()->create([
            'company_id'  => $company->id,
            'category_id' => $category->id,
        ]);

        $candidate = User::factory()->create(['role' => 'candidate', 'status' => 'active']);
        Application::factory()->create([
            'job_id'       => $job->id,
            'candidate_id' => $candidate->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/recruiter/jobs/{$job->id}/applications");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_recruiter_can_update_application_status(): void
    {
        [$user, $company] = $this->recruiterWithCompany();
        $category  = Category::factory()->create();
        $job       = Job::factory()->create([
            'company_id'  => $company->id,
            'category_id' => $category->id,
        ]);
        $candidate   = User::factory()->create(['role' => 'candidate', 'status' => 'active']);
        $application = Application::factory()->create([
            'job_id'       => $job->id,
            'candidate_id' => $candidate->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/recruiter/applications/{$application->id}/status", [
                'status' => 'reviewed',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_recruiter_cannot_set_pending_status(): void
    {
        [$user, $company] = $this->recruiterWithCompany();
        $category  = Category::factory()->create();
        $job       = Job::factory()->create([
            'company_id'  => $company->id,
            'category_id' => $category->id,
        ]);
        $candidate   = User::factory()->create(['role' => 'candidate', 'status' => 'active']);
        $application = Application::factory()->create([
            'job_id'       => $job->id,
            'candidate_id' => $candidate->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/recruiter/applications/{$application->id}/status", [
                'status' => 'pending',
            ]);

        $response->assertStatus(422);
    }

    public function test_recruiter_cannot_access_other_recruiters_jobs(): void
    {
        [$user, $company] = $this->recruiterWithCompany();

        // Another recruiter owns this job
        $otherUser    = User::factory()->create(['role' => 'recruiter', 'status' => 'active']);
        $otherCompany = Company::factory()->create(['user_id' => $otherUser->id]);
        $category     = Category::factory()->create();
        $otherJob     = Job::factory()->create([
            'company_id'  => $otherCompany->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/recruiter/jobs/{$otherJob->id}");

        // Should be 404 (not found within recruiter's own company scope)
        $response->assertStatus(404);
    }

    public function test_recruiter_dashboard_stats(): void
    {
        [$user, $company] = $this->recruiterWithCompany();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/recruiter/dashboard-stats');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => []]);
    }
}
