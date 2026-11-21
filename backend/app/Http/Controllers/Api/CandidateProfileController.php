<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateProfileController extends Controller
{
    /**
     * GET /api/candidate/profile
     * Retrieve current candidate's profile and user info.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('candidateProfile');

        return $this->success('Candidate profile retrieved.', [
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'avatar'     => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'status'     => $user->status,
                'created_at' => $user->created_at,
            ],
            'profile' => $this->formatProfile($user->candidateProfile),
        ]);
    }

    /**
     * POST /api/candidate/profile
     * Update personal info, bio, education, experience_years, avatar, and default_cv_path (.pdf).
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name'             => 'sometimes|string|max:255',
            'phone'            => 'sometimes|nullable|string|max:20',
            'dob'              => 'sometimes|nullable|date',
            'gender'           => 'sometimes|nullable|in:male,female,other',
            'address'          => 'sometimes|nullable|string|max:255',
            'bio'              => 'sometimes|nullable|string',
            'education'        => 'sometimes|nullable|string',
            'experience_years' => 'sometimes|nullable|integer|min:0|max:50',
            'skills_text'      => 'sometimes|nullable|string|max:500',
            'avatar'           => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'default_cv'       => 'sometimes|nullable|file|mimes:pdf|max:5120',
        ]);

        $user = $request->user();

        // Update user fields
        $userFields = $request->only(['name', 'phone']);
        if (! empty($userFields)) {
            $user->fill($userFields)->save();
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
            $user->save();
        }

        // Update or create candidate profile
        $profile = $user->candidateProfile ?? CandidateProfile::create(['user_id' => $user->id]);

        $profileFields = $request->only([
            'dob', 'gender', 'address', 'bio',
            'education', 'experience_years', 'skills_text',
        ]);
        $profile->fill($profileFields);

        // Handle default CV upload (PDF only)
        if ($request->hasFile('default_cv')) {
            if ($profile->default_cv_path) {
                Storage::disk('public')->delete($profile->default_cv_path);
            }
            $cvPath = $request->file('default_cv')->store('cvs', 'public');
            $profile->default_cv_path = $cvPath;
        }

        $profile->save();

        $user->refresh();

        return $this->success('Profile updated successfully.', [
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'avatar'     => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'status'     => $user->status,
            ],
            'profile' => $this->formatProfile($profile),
        ]);
    }

    /**
     * Format the candidate profile for API responses.
     */
    private function formatProfile(?CandidateProfile $profile): ?array
    {
        if (! $profile) {
            return null;
        }

        return [
            'id'               => $profile->id,
            'dob'              => $profile->dob?->toDateString(),
            'gender'           => $profile->gender,
            'address'          => $profile->address,
            'bio'              => $profile->bio,
            'education'        => $profile->education,
            'experience_years' => $profile->experience_years,
            'skills_text'      => $profile->skills_text,
            'default_cv_path'  => $profile->default_cv_path
                ? asset('storage/' . $profile->default_cv_path)
                : null,
        ];
    }
}

