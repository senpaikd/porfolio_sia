<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    public function definition(): array
    {
        return ['company' => 'Placeholder: Company', 'role' => 'Placeholder: Role', 'start_date' => now()->subYear(), 'description' => 'Placeholder: Describe your work.', 'is_published' => true, 'display_order' => 0];
    }
}
