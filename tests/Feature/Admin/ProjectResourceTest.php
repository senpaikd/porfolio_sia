<?php

namespace Tests\Feature\Admin;

use App\Enums\ProjectStatus;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Models\Technology;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_administrator_can_create_project_with_technologies(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $technology = Technology::factory()->create();

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Managed Project',
                'slug' => 'managed-project',
                'short_description' => 'A concise factual project summary.',
                'full_description' => 'A complete factual description of the project and its implementation.',
                'status' => ProjectStatus::Completed->value,
                'is_featured' => true,
                'is_published' => true,
                'display_order' => 4,
                'technologies' => [$technology->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', ['slug' => 'managed-project', 'is_published' => true]);
        $this->assertDatabaseHas('project_technology', ['technology_id' => $technology->id]);
    }

    public function test_project_resource_reports_required_and_unsafe_url_validation_errors(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(CreateProject::class)
            ->fillForm(['title' => '', 'github_url' => 'javascript:alert(1)'])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required', 'short_description' => 'required', 'full_description' => 'required', 'github_url']);
    }

    public function test_administrator_can_create_an_unfeatured_draft(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Draft Project',
                'slug' => 'draft-project',
                'short_description' => 'A draft project summary.',
                'full_description' => 'A draft project description that remains private.',
                'status' => ProjectStatus::InDevelopment->value,
                'is_featured' => false,
                'is_published' => false,
                'display_order' => 0,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', ['slug' => 'draft-project', 'is_featured' => false, 'is_published' => false]);
    }

    public function test_non_administrator_cannot_open_project_creation_page(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->get('/admin/projects/create')->assertForbidden();
    }

    public function test_project_main_image_rejects_non_image_uploads(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Invalid Upload',
                'slug' => 'invalid-upload',
                'short_description' => 'A project with an invalid upload.',
                'full_description' => 'The invalid upload must be rejected before persistence.',
                'status' => ProjectStatus::Completed->value,
                'main_image' => UploadedFile::fake()->create('payload.pdf', 100, 'application/pdf'),
            ])
            ->call('create')
            ->assertHasFormErrors(['main_image']);

        $this->assertDatabaseMissing('projects', ['slug' => 'invalid-upload']);
    }
}
