<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * Register a new user (candidate or recruiter).
     * Auto-create empty profile/draft company on registration.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:candidate,recruiter',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'role'     => $validated['role'],
            'status'   => 'active',
        ]);

        // Auto-create empty profile based on role
        if ($user->role === 'candidate') {
            CandidateProfile::create(['user_id' => $user->id]);
        } elseif ($user->role === 'recruiter') {
            Company::create([
                'user_id'  => $user->id,
                'name'     => $user->name . "'s Company",
                'slug'     => Str::slug($user->name . '-' . $user->id . '-company'),
                'address'  => 'Not specified',
                'city'     => 'Not specified',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success('Registration successful.', [
            'token' => $token,
            'user'  => $this->formatUser($user),
        ], 201);
    }

    /**
     * POST /api/auth/login
     * Authenticate credentials and return Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->error('Invalid email or password.', null, 401);
        }

        if ($user->status === 'banned') {
            return $this->error('Your account has been banned. Please contact support.', null, 403);
        }

        // Revoke old tokens and issue a fresh one
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success('Login successful.', [
            'token' => $token,
            'user'  => $this->formatUser($user),
        ]);
    }

    /**
     * POST /api/auth/logout
     * Revoke the current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return $this->success('Logged out successfully.');
    }

    /**
     * GET /api/auth/me
     * Return the authenticated user with their profile/company relation.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $userData = $this->formatUser($user);

        if ($user->role === 'candidate') {
            $userData['candidate_profile'] = $user->candidateProfile;
        } elseif ($user->role === 'recruiter') {
            $userData['company'] = $user->company;
        }

        return $this->success('User profile retrieved.', $userData);
    }

    /**
     * Format a user object for API responses (hide sensitive fields).
     */
    private function formatUser(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'phone'      => $user->phone,
            'avatar'     => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'status'     => $user->status,
            'created_at' => $user->created_at,
        ];
    }
}

