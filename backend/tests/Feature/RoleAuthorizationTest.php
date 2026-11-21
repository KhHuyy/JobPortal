<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function candidate(): User
    {
        return User::factory()->create(['role' => 'candidate', 'status' => 'active']);
    }

    private function recruiter(): User
    {
        $user = User::factory()->create(['role' => 'recruiter', 'status' => 'active']);
        Company::factory()->create(['user_id' => $user->id]);
        return $user;
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    // ── Guest ──────────────────────────────────────────────────────────────

    public function test_guest_cannot_access_candidate_routes(): void
    {
        $this->getJson('/api/candidate/profile')->assertStatus(401);
    }

    public function test_guest_cannot_access_recruiter_routes(): void
    {
        $this->getJson('/api/recruiter/jobs')->assertStatus(401);
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $this->getJson('/api/admin/dashboard-stats')->assertStatus(401);
    }

    // ── Cross-role protection ──────────────────────────────────────────────

    public function test_candidate_cannot_access_recruiter_routes(): void
    {
        $this->actingAs($this->candidate(), 'sanctum')
            ->getJson('/api/recruiter/jobs')
            ->assertStatus(403);
    }

    public function test_candidate_cannot_access_admin_routes(): void
    {
        $this->actingAs($this->candidate(), 'sanctum')
            ->getJson('/api/admin/users')
            ->assertStatus(403);
    }

    public function test_recruiter_cannot_access_candidate_routes(): void
    {
        $this->actingAs($this->recruiter(), 'sanctum')
            ->getJson('/api/candidate/profile')
            ->assertStatus(403);
    }

    public function test_recruiter_cannot_access_admin_routes(): void
    {
        $this->actingAs($this->recruiter(), 'sanctum')
            ->getJson('/api/admin/dashboard-stats')
            ->assertStatus(403);
    }

    public function test_admin_cannot_access_candidate_routes(): void
    {
        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/candidate/profile')
            ->assertStatus(403);
    }

    // ── Admin access ───────────────────────────────────────────────────────

    public function test_admin_can_access_all_admin_routes(): void
    {
        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/admin/dashboard-stats')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_list_users(): void
    {
        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/admin/users')
            ->assertStatus(200);
    }

    public function test_admin_can_list_categories(): void
    {
        Category::factory()->count(3)->create();

        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/admin/categories')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    // ── Banned user blocked ────────────────────────────────────────────────

    public function test_banned_candidate_cannot_access_own_routes(): void
    {
        $user = User::factory()->create(['role' => 'candidate', 'status' => 'banned']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/candidate/profile')
            ->assertStatus(403);
    }
}
