<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiteSettingFactory extends Factory
{
    public function definition(): array
    {
        return ['owner_name' => 'Placeholder: Your Name', 'professional_title' => 'Placeholder: Professional Title', 'biography' => 'Placeholder: Add your biography.', 'email' => 'placeholder@example.test'];
    }
}
