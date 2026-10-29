<?php

namespace Database\Factories;

use App\Models\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'category' => fake()->randomElement(['Circular', 'Act', 'Gazette', 'Report', 'Handbook', 'Application Form']),
            'document_number' => 'WBB/DOC/'.fake()->numerify('####'),
            'language' => fake()->randomElement(['English', 'Sinhala', 'Tamil', 'Trilingual']),
            'description' => fake()->paragraph(),
            'file_path' => null,
            'file_url' => null,
            'file_size' => null,
            'published_date' => fake()->date(),
            'is_active' => true,
        ];
    }
}
