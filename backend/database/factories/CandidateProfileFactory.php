<?php

namespace Database\Factories;

use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CandidateProfile> */
class CandidateProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'dob'              => fake()->date(),
            'gender'           => 'male',
            'address'          => fake()->address(),
            'bio'              => fake()->paragraph(),
            'education'        => 'B.Sc. Computer Science',
            'experience_years' => fake()->numberBetween(0, 10),
            'skills_text'      => 'PHP, Laravel, Vue.js',
            'default_cv_path'  => null,
        ];
    }
}
