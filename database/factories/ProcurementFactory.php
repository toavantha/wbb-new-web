<?php

namespace Database\Factories;

use App\Models\Procurement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Procurement>
 */
class ProcurementFactory extends Factory
{
    protected $model = Procurement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'title_si' => null,
            'title_ta' => null,
            'reference_no' => 'WBB/PROC/'.fake()->year().'/'.fake()->unique()->numberBetween(10, 999),
            'category' => fake()->randomElement(Procurement::CATEGORIES),
            'description' => fake()->paragraph(),
            'description_si' => null,
            'description_ta' => null,
            'file_path' => 'procurements/sample_en.pdf',
            'file_path_si' => null,
            'file_path_ta' => null,
            'file_size' => '245 KB',
            'file_size_si' => null,
            'file_size_ta' => null,
            'published_date' => Carbon::today()->subDays(fake()->numberBetween(1, 14)),
            'deadline_date' => Carbon::today()->addDays(fake()->numberBetween(5, 30)),
            'original_deadline_date' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the procurement has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline_date' => Carbon::today()->subDays(5),
            'published_date' => Carbon::today()->subDays(25),
        ]);
    }

    /**
     * Indicate that the procurement deadline was extended.
     */
    public function extended(): static
    {
        return $this->state(fn (array $attributes) => [
            'original_deadline_date' => Carbon::today()->subDays(2),
            'deadline_date' => Carbon::today()->addDays(14),
        ]);
    }

    /**
     * Indicate that the procurement has Sinhala and Tamil translations.
     */
    public function trilingual(): static
    {
        return $this->state(fn (array $attributes) => [
            'title_si' => 'ටෙන්ඩර් දැන්වීම - '.fake()->sentence(4),
            'title_ta' => 'கொள்முதல் அறிவித்தல் - '.fake()->sentence(4),
            'description_si' => 'සුබසාධක ප්‍රතිලාභ මණ්ඩලය සඳහා අවශ්‍ය උපකරණ මිලදී ගැනීම.',
            'description_ta' => 'நலன்புரி நன்மைகள் சபைக்கான உபகரணங்கள் கொள்வனவு.',
            'file_path_si' => 'procurements/sample_si.pdf',
            'file_path_ta' => 'procurements/sample_ta.pdf',
            'file_size_si' => '250 KB',
            'file_size_ta' => '260 KB',
        ]);
    }
}
