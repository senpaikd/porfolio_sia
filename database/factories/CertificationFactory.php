<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CertificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(3),
            'issuer' => 'Cisco Networking Academy',
            'completion_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'certificate_path' => null,
            'is_featured' => false,
            'is_published' => true,
            'display_order' => 0,
        ];
    }
}
