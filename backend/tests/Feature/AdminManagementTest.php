<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Job;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ helpers

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    private function pendingJob(): Job
    {
        $recruiter = User::factory()->create(['role' => 'recruiter', 'status' => 'active']);
        $company   = Company::factory()->create(['user_id' => $recruiter->id]);
        $category  = Category::factory()->create();
        return Job::factory()->create([
            'company_id'  => $company->id,
            'category_id' => $category->id,
            'status'      => 'pending',
        ]);
    }

    // ------------------------------------------------------------------ dashboard

    public function test_admin_can_view_dashboard_stats(): void
    {
        $admin    = $this->admin();
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/dashboard-stats');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'total_candidates',
                    'total_recruiters',
                    'total_jobs',
                    'active_jobs',
                    'pending_jobs',
                    'total_applications',
                ],
            ]);
    }

    // ------------------------------------------------------------------ jobs

    public function test_admin_can_list_all_jobs(): void
    {
        $admin    = $this->admin();
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/jobs');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_filter_jobs_by_status(): void
    {
        $admin = $this->admin();
        $this->pendingJob();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/jobs?status=pending');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_approve_job(): void
    {
        $admin = $this->admin();
        $job   = $this->pendingJob();

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/jobs/{$job->id}/status", ['status' => 'active']);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_reject_job(): void
    {
        $admin = $this->admin();
        $job   = $this->pendingJob();

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/jobs/{$job->id}/status", ['status' => 'rejected']);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_cannot_set_invalid_job_status(): void
    {
        $admin = $this->admin();
        $job   = $this->pendingJob();

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/jobs/{$job->id}/status", ['status' => 'approved']);

        $response->assertStatus(422);
    }

    // ------------------------------------------------------------------ users

    public function test_admin_can_list_users(): void
    {
        $admin    = $this->admin();
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_ban_user(): void
    {
        $admin     = $this->admin();
        $candidate = User::factory()->create(['role' => 'candidate', 'status' => 'active']);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/users/{$candidate->id}/toggle-ban");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'banned');
    }

    public function test_admin_can_unban_user(): void
    {
        $admin     = $this->admin();
        $candidate = User::factory()->create(['role' => 'candidate', 'status' => 'banned']);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/users/{$candidate->id}/toggle-ban");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'active');
    }

    public function test_admin_cannot_ban_another_admin(): void
    {
        $admin      = $this->admin();
        $otherAdmin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/users/{$otherAdmin->id}/toggle-ban");

        $response->assertStatus(403);
    }

    // ------------------------------------------------------------------ categories

    public function test_admin_can_create_category(): void
    {
        $admin    = $this->admin();
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/categories', ['name' => 'Test Category']);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_update_category(): void
    {
        $admin    = $this->admin();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/categories/{$category->id}", ['name' => 'Updated Category']);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $admin    = $this->admin();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_cannot_delete_category_with_jobs(): void
    {
        $admin    = $this->admin();
        $category = Category::factory()->create();

        $recruiter = User::factory()->create(['role' => 'recruiter', 'status' => 'active']);
        $company   = Company::factory()->create(['user_id' => $recruiter->id]);
        Job::factory()->create([
            'company_id'  => $company->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/categories/{$category->id}");

        $response->assertStatus(409);
    }

    // ------------------------------------------------------------------ skills

    public function test_admin_can_create_skill(): void
    {
        $admin    = $this->admin();
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/skills', ['name' => 'TestSkill99']);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_update_skill(): void
    {
        $admin = $this->admin();
        $skill = Skill::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/skills/{$skill->id}", ['name' => 'UpdatedSkill']);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_delete_skill(): void
    {
        $admin = $this->admin();
        $skill = Skill::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/skills/{$skill->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
