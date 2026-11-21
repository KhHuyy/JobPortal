<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CandidateProfile;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ── Registration ───────────────────────────────────────────────────────

    public function test_guest_can_register_as_candidate(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'candidate',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'data' => ['token', 'user']])
            ->assertJsonPath('data.user.role', 'candidate');

        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'role' => 'candidate']);
        $this->assertDatabaseHas('candidate_profiles', []);
    }

    public function test_guest_can_register_as_recruiter(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'HR Manager',
            'email'                 => 'hr@company.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'recruiter',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.role', 'recruiter');

        $this->assertDatabaseHas('companies', []);
    }

    public function test_registration_fails_with_invalid_data(): void
    {
        $this->postJson('/api/auth/register', [
            'name'  => '',
            'email' => 'not-an-email',
        ])->assertStatus(422)
          ->assertJsonPath('success', false);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/auth/register', [
            'name'                  => 'Another',
            'email'                 => 'taken@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'candidate',
        ])->assertStatus(422);
    }

    // ── Login ──────────────────────────────────────────────────────────────

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'login@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'candidate',
            'status'   => 'active',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['token', 'user']])
            ->assertJsonPath('data.user.email', 'login@example.com');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'x@example.com', 'password' => bcrypt('correct')]);

        $this->postJson('/api/auth/login', [
            'email'    => 'x@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(401);
    }

    public function test_banned_user_cannot_login(): void
    {
        User::factory()->create([
            'email'    => 'banned@example.com',
            'password' => bcrypt('password123'),
            'status'   => 'banned',
        ]);

        $this->postJson('/api/auth/login', [
            'email'    => 'banned@example.com',
            'password' => 'password123',
        ])->assertStatus(403);
    }

    // ── Logout & Me ────────────────────────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'candidate', 'status' => 'active']);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/auth/logout')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_authenticated_user_can_get_me(): void
    {
        $user = User::factory()->create(['role' => 'candidate', 'status' => 'active']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/me')
            ->assertStatus(200)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_unauthenticated_request_to_me_returns_401(): void
    {
        $this->getJson('/api/auth/me')->assertStatus(401);
    }
}
