<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role'     => 'candidate',
            'status'   => 'active',
        ];
    }

    /**
     * State: recruiter role.
     */
    public function recruiter(): static
    {
        return $this->state(['role' => 'recruiter']);
    }

    /**
     * State: admin role.
     */
    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    /**
     * State: banned status.
     */
    public function banned(): static
    {
        return $this->state(['status' => 'banned']);
    }
}
