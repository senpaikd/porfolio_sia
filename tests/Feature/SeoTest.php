<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_has_canonical_and_open_graph_metadata(): void
    {
        SiteSetting::factory()->create(['seo_title' => 'Portfolio title', 'seo_description' => 'Portfolio description']);

        $this->get(route('home'))
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
            ->assertSee('<meta property="og:title" content="Portfolio title">', false)
            ->assertSee('<meta property="og:description" content="Portfolio description">', false);
    }

    public function test_project_metadata_uses_project_overrides_and_image(): void
    {
        $project = Project::factory()->create([
            'seo_title' => 'Custom project title',
            'seo_description' => 'Custom project description',
            'main_image' => 'projects/main/project.webp',
        ]);

        $this->get(route('projects.show', $project))
            ->assertSee('<meta property="og:title" content="Custom project title">', false)
            ->assertSee('<meta property="og:description" content="Custom project description">', false)
            ->assertSee('<meta property="og:image"', false)
            ->assertSee('<link rel="canonical" href="'.route('projects.show', $project).'">', false);
    }
}
