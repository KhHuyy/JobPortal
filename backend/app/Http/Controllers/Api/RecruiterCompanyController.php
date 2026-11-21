<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecruiterCompanyController extends Controller
{
    /**
     * GET /api/recruiter/company
     * Retrieve the recruiter's company profile.
     */
    public function show(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        if (! $company) {
            return $this->error('Company profile not found.', null, 404);
        }

        // Build full URL for logo (consistent with the update() response)
        $companyData = $company->toArray();
        $companyData['logo'] = $company->logo ? asset('storage/' . $company->logo) : null;

        return $this->success('Company profile retrieved.', $companyData);
    }

    /**
     * POST /api/recruiter/company
     * Update company details and optionally upload a company logo.
     * Logo must be .jpg, .png, or .webp (max 2MB).
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name'         => 'sometimes|string|max:255',
            'tax_code'     => 'sometimes|nullable|string|max:50',
            'website'      => 'sometimes|nullable|url|max:255',
            'company_size' => 'sometimes|nullable|string|max:100',
            'address'      => 'sometimes|string|max:255',
            'city'         => 'sometimes|string|max:100',
            'description'  => 'sometimes|nullable|string',
            'logo'         => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $company = $request->user()->company;

        if (! $company) {
            return $this->error('Company profile not found.', null, 404);
        }

        $fields = $request->only([
            'name', 'tax_code', 'website',
            'company_size', 'address', 'city', 'description',
        ]);

        // Regenerate slug if name changes
        if (isset($fields['name']) && $fields['name'] !== $company->name) {
            $fields['slug'] = Str::slug($fields['name']);

            // Ensure slug uniqueness
            $slugBase = $fields['slug'];
            $count = 1;
            while (Company::where('slug', $fields['slug'])->where('id', '!=', $company->id)->exists()) {
                $fields['slug'] = $slugBase . '-' . $count++;
            }
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $fields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->fill($fields)->save();

        $company->refresh();
        $company->logo = $company->logo ? asset('storage/' . $company->logo) : null;

        return $this->success('Company profile updated successfully.', $company);
    }
}

