<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'slug' => null,
            'short_description' => fake()->sentence(),
            'full_description' => fake()->paragraphs(2, true),
            'status' => ProjectStatus::Completed,
            'is_featured' => false,
            'is_published' => true,
            'display_order' => 0,
            'completion_date' => fake()->date(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
