<?php

namespace Database\Factories;

use App\Models\WorkUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkUnit>
 */
class WorkUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company().' Bureau',
            'code' => strtoupper(fake()->unique()->lexify('???-###')),
            'campus' => fake()->randomElement(['Babarsari Campus', 'Condongcatur Campus']),
            'type' => fake()->randomElement(['bureau', 'faculty', 'directorate', 'rectorate']),
        ];
    }
}
