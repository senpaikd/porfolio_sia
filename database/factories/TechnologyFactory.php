<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TechnologyFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->word(), 'slug' => null, 'is_published' => true, 'display_order' => 0];
    }
}
