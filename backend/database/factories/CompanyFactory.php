<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Company> */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'user_id'      => User::factory(),
            'name'         => $name,
            'slug'         => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'tax_code'     => fake()->numerify('##########'),
            'website'      => fake()->url(),
            'company_size' => '11-50',
            'address'      => fake()->streetAddress(),
            'city'         => fake()->city(),
            'description'  => fake()->paragraph(),
            'is_verified'  => true,
        ];
    }
}
