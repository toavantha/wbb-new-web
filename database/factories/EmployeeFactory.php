<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'designation' => fake()->jobTitle(),
            'mobile' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'image_path' => null,
            'is_active' => true,
        ];
    }
}
