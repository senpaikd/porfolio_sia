<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFactory extends Factory
{
    public function definition(): array
    {
        return ['institution' => 'Placeholder: Institution', 'qualification' => 'Placeholder: Qualification', 'description' => 'Placeholder: Add education details.', 'is_published' => true, 'display_order' => 0];
    }
}
