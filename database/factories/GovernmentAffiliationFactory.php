<?php

namespace Database\Factories;

use App\Models\GovernmentAffiliation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GovernmentAffiliation>
 */
class GovernmentAffiliationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_en' => fake()->name(),
            'name_si' => fake()->name(),
            'name_ta' => fake()->name(),
            'title_en' => fake()->jobTitle(),
            'title_si' => fake()->jobTitle(),
            'title_ta' => fake()->jobTitle(),
            'ministry_en' => 'Ministry of Finance, Planning and Economic Development',
            'ministry_si' => 'මුදල්, සැලසුම් සහ ආර්ථික සංවර්ධන අමාත්‍යාංශය',
            'ministry_ta' => 'நிதி, திட்டமிடல் மற்றும் பொருளாதார அபிவிருத்தி அமைச்சு',
            'image_path' => null,
            'sort_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
