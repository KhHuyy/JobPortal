<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Application> */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_id'         => Job::factory(),
            'candidate_id'   => User::factory(),
            'cv_file_path'   => 'cvs/test.pdf',
            'cover_letter'   => fake()->paragraph(),
            'status'         => 'pending',
            'recruiter_note' => null,
        ];
    }
}
