<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * GET /api/admin/users
     * List all user accounts with filtering by role and keyword.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['candidateProfile', 'company:id,user_id,name,slug,is_verified'])
            ->orderByDesc('created_at');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$kw}%")
                ->orWhere('email', 'like', "%{$kw}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15)->through(function ($user) {
            return [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'phone'      => $user->phone,
                'avatar'     => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'status'     => $user->status,
                'created_at' => $user->created_at,
                'profile'    => $user->role === 'candidate' ? $user->candidateProfile : null,
                'company'    => $user->role === 'recruiter' ? $user->company : null,
            ];
        });

        return $this->success('Users retrieved.', $users);
    }

    /**
     * PATCH /api/admin/users/{id}/toggle-ban
     * Ban or unban a user account.
     * Toggles status between 'active' and 'banned'.
     */
    public function toggleBan(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Prevent banning other admins
        if ($user->role === 'admin') {
            return $this->error('Admin accounts cannot be banned.', null, 403);
        }

        $user->status = $user->status === 'active' ? 'banned' : 'active';
        $user->save();

        // Revoke all tokens if banned
        if ($user->status === 'banned') {
            $user->tokens()->delete();
        }

        $action = $user->status === 'banned' ? 'banned' : 'unbanned';

        return $this->success("User account {$action} successfully.", [
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'status' => $user->status,
        ]);
    }
}

