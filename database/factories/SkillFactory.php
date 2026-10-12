<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->word(), 'category' => 'Placeholder: Category', 'is_published' => true, 'display_order' => 0];
    }
}
