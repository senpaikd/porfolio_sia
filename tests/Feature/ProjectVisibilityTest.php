<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_project_slug_resolves(): void
    {
        $project = Project::factory()->create(['title' => 'Visible Project']);

        $this->get(route('projects.show', $project))->assertOk()->assertSee('Visible Project');
    }

    public function test_draft_and_missing_project_slugs_return_404(): void
    {
        $draft = Project::factory()->draft()->create();

        $this->get('/projects/'.$draft->slug)->assertNotFound();
        $this->get('/projects/not-real')->assertNotFound();
    }

    public function test_project_becomes_unavailable_immediately_after_unpublishing(): void
    {
        $project = Project::factory()->create();
        $project->update(['is_published' => false]);

        $this->get(route('projects.show', $project))->assertNotFound();
    }
}
