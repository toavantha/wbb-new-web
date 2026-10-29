<?php

namespace Database\Factories;

use App\Models\WelfareScheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WelfareScheme>
 */
class WelfareSchemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => [
                'en' => [
                    'name' => fake()->words(3, true),
                    'badge' => fake()->word(),
                    'duration' => fake()->words(2, true),
                    'description' => fake()->sentence(),
                    'indicators' => [fake()->sentence()],
                ],
                'si' => [
                    'name' => 'සුබසාධන යෝජනා ක්‍රමය',
                    'badge' => 'ප්‍රතිලාභය',
                    'duration' => 'අඛණ්ඩව',
                    'description' => 'සුබසාධන ප්‍රතිලාභ වැඩසටහන.',
                    'indicators' => ['සුදුසුකම් තහවුරු කිරීම'],
                ],
                'ta' => [
                    'name' => 'நலன்புரித் திட்டம்',
                    'badge' => 'நன்மை',
                    'duration' => 'தொடர்ச்சியாக',
                    'description' => 'நலன்புரி நன்மைத் திட்டம்.',
                    'indicators' => ['தகுதியை உறுதிப்படுத்தல்'],
                ],
            ],
            'monthly_allowance' => fake()->numberBetween(1000, 15000),
            'sort_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
