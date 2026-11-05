<?php

namespace Database\Factories;

use App\Models\CurrentAffair;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CurrentAffair>
 */
class CurrentAffairFactory extends Factory
{
    protected $model = CurrentAffair::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = $this->faker->numberBetween(2024, 2026);
        $month = $this->faker->numberBetween(1, 12);
        $monthName = CurrentAffair::getMonths()[$month];

        return [
            'title' => "Monthly Current Affairs - {$monthName} {$year}",
            'year' => $year,
            'month' => $month,
            'description' => $this->faker->paragraphs(2, true),
            'pdf_path' => 'current-affairs/pdfs/sample-' . $year . '-' . $month . '.pdf',
            'pdf_name' => "AptiMedha-Current-Affairs-{$monthName}-{$year}.pdf",
            'preview_image' => null,
            'preview_image_alt' => "Current Affairs {$monthName} {$year} Cover",
            'file_size_bytes' => $this->faker->numberBetween(1024 * 500, 1024 * 1024 * 5),
            'downloads_count' => $this->faker->numberBetween(10, 500),
            'is_published' => true,
            'published_at' => now(),
        ];
    }

    /**
     * State for unpublished records.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}

