<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_contains_public_routes_and_only_published_projects(): void
    {
        $published = Project::factory()->create();
        $draft = Project::factory()->draft()->create();

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('home'), false)
            ->assertSee(route('projects.index'), false)
            ->assertSee(route('projects.show', $published), false)
            ->assertDontSee($draft->slug);
    }
}
