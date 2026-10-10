<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition(): array
    {
        $spheres = [-3.50, -3.00, -2.50, -2.00, -1.50, -1.00, -0.50, 0.00, +0.50, +1.00, +1.50, +2.00, +2.50, +3.00];
        $cylinders = [-1.50, -1.25, -1.00, -0.75, -0.50, -0.25, 0.00];
        $additions = [null, 0.75, 1.00, 1.25, 1.50, 1.75, 2.00, 2.25, 2.50];

        return [
            'client_id' => Client::factory(),
            'prescription_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'od_sphere' => fake()->randomElement($spheres),
            'od_cylinder' => fake()->randomElement($cylinders),
            'od_axis' => fake()->numberBetween(0, 180),
            'od_addition' => fake()->randomElement($additions),
            'og_sphere' => fake()->randomElement($spheres),
            'og_cylinder' => fake()->randomElement($cylinders),
            'og_axis' => fake()->numberBetween(0, 180),
            'og_addition' => fake()->randomElement($additions),
            'pd' => fake()->randomFloat(1, 58, 68),
            'pd_right' => fake()->randomFloat(1, 29, 34),
            'pd_left' => fake()->randomFloat(1, 29, 34),
            'prescription_file_path' => null,
            'prescribed_by' => 'Dr. ' . fake()->lastName(),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}
