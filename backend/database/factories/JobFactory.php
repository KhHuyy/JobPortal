<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Job> */
class JobFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->jobTitle() . ' ' . fake()->numerify('###');
        return [
            'company_id'       => Company::factory(),
            'category_id'      => Category::factory(),
            'title'            => $title,
            'slug'             => Str::slug($title),
            'job_type'         => fake()->randomElement(['full_time', 'part_time', 'remote', 'internship']),
            'experience_level' => fake()->randomElement(['Entry Level', '1-2 Years', 'Senior (5+ Years)']),
            'salary_min'       => 1000,
            'salary_max'       => 3000,
            'is_negotiable'    => false,
            'location'         => fake()->city(),
            'work_address'     => fake()->streetAddress(),
            'vacancies'        => 1,
            'description'      => fake()->paragraph(3),
            'requirements'     => fake()->paragraph(2),
            'benefits'         => fake()->paragraph(2),
            'deadline'         => now()->addDays(30)->toDateString(),
            'status'           => 'active',
            'views_count'      => 0,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }
}
