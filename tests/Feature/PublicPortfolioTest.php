<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_and_project_gallery_load(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('projects.index'))->assertOk();
    }

    public function test_homepage_only_shows_published_featured_projects_in_order(): void
    {
        $second = Project::factory()->create(['title' => 'Second', 'is_featured' => true, 'display_order' => 20]);
        $first = Project::factory()->create(['title' => 'First', 'is_featured' => true, 'display_order' => 1]);
        $ordinary = Project::factory()->create(['title' => 'Ordinary', 'is_featured' => false]);
        $draft = Project::factory()->create(['title' => 'Draft featured', 'is_featured' => true, 'is_published' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder([$first->title, $second->title])
            ->assertDontSee($ordinary->title)
            ->assertDontSee($draft->title);
    }

    public function test_gallery_is_paginated_and_ordered(): void
    {
        Project::factory()->count(13)->create();

        $this->get(route('projects.index'))->assertOk()->assertViewHas('projects', fn ($projects) => $projects->perPage() === 12 && $projects->total() === 13);
    }
}
