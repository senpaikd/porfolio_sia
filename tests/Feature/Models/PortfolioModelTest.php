<?php

namespace Tests\Feature\Models;

use App\Enums\ProjectStatus;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Technology;
use Database\Seeders\PortfolioContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_relationships_scopes_and_ordering_are_consistent(): void
    {
        $draft = Project::factory()->create(['title' => 'Draft', 'is_published' => false, 'display_order' => 0]);
        $later = Project::factory()->create(['title' => 'Later', 'is_published' => true, 'is_featured' => true, 'display_order' => 20]);
        $first = Project::factory()->create(['title' => 'First', 'is_published' => true, 'is_featured' => true, 'display_order' => 5]);
        $technology = Technology::factory()->create();
        $first->technologies()->attach($technology);
        $image = $first->images()->create(['path' => 'projects/screenshots/example.webp', 'alt_text' => 'Example', 'display_order' => 1]);

        $this->assertSame([$first->id, $later->id], Project::published()->ordered()->pluck('id')->all());
        $this->assertSame([$first->id, $later->id], Project::featured()->ordered()->pluck('id')->all());
        $this->assertFalse(Project::published()->whereKey($draft)->exists());
        $this->assertTrue($first->technologies->contains($technology));
        $this->assertTrue($first->images->contains($image));
        $this->assertSame('slug', $first->getRouteKeyName());
    }

    public function test_project_slugs_are_generated_and_collisions_are_suffixed(): void
    {
        $first = Project::factory()->create(['title' => 'My Project', 'slug' => null]);
        $second = Project::factory()->create(['title' => 'My Project', 'slug' => null]);

        $this->assertSame('my-project', $first->slug);
        $this->assertSame('my-project-2', $second->slug);
    }

    public function test_site_settings_expose_a_safe_singleton(): void
    {
        $this->assertStringContainsString('Placeholder', SiteSetting::current()->owner_name);
        $this->assertSame(SiteSetting::current()->id, SiteSetting::current()->id);
    }

    public function test_portfolio_seeder_publishes_factual_projects_and_keeps_seawisehub_private(): void
    {
        $this->seed(PortfolioContentSeeder::class);

        $settings = SiteSetting::current();
        $docuMind = Project::query()->where('title', 'DocuMind')->firstOrFail();
        $helpDesk = Project::query()->where('title', 'HelpDesk Ticketing System')->firstOrFail();
        $seawiseHub = Project::query()->where('title', 'SeawiseHub')->firstOrFail();

        $this->assertSame('Kent C. Dayola', $settings->owner_name);
        $this->assertSame('Entry-level Web Developer', $settings->professional_title);
        $this->assertSame('kentdayola@gmail.com', $settings->email);
        $this->assertSame('https://github.com/senpaikd', $settings->github_url);
        $this->assertNull($settings->linkedin_url);
        $this->assertNull($settings->resume_path);

        $this->assertDatabaseHas('experiences', [
            'company' => 'Abenales.tech',
            'role' => 'Junior Front-End Developer',
            'is_published' => true,
        ]);
        $this->assertDatabaseHas('experiences', [
            'company' => 'KND Resource Management Corp. / Chemtrust Global Market Inc.',
            'role' => 'IT Support Intern (OJT)',
            'is_published' => true,
        ]);
        $this->assertSame(2, Experience::query()->count());

        $this->assertDatabaseHas('education', [
            'institution' => 'Southern Leyte State University - Bontoc Campus',
            'qualification' => 'Bachelor of Science in Information Technology',
            'is_published' => true,
        ]);
        $this->assertSame(1, Education::query()->count());

        $this->assertTrue(Skill::query()->where('name', 'Laravel')->where('category', 'Backend / Data')->exists());
        $this->assertTrue(Skill::query()->where('name', 'Responsive Web Design')->where('category', 'Frontend')->exists());
        $this->assertFalse(Skill::query()->where('name', 'like', 'Placeholder:%')->exists());

        $this->assertSame(ProjectStatus::InDevelopment, $docuMind->status);
        $this->assertTrue($docuMind->is_published);
        $this->assertTrue($docuMind->is_featured);
        $this->assertNull($docuMind->github_url);
        $this->assertSame('https://documind-bay-kappa.vercel.app/', $docuMind->live_url);
        $this->assertStringNotContainsString('Placeholder', $docuMind->short_description);
        $this->assertStringContainsString('privacy-conscious AI document assistant', $docuMind->short_description);
        $this->assertEqualsCanonicalizing(
            ['AI Integration', 'Next.js', 'OpenAI'],
            $docuMind->technologies()->pluck('name')->all(),
        );

        $this->assertSame(ProjectStatus::Completed, $helpDesk->status);
        $this->assertTrue($helpDesk->is_published);
        $this->assertTrue($helpDesk->is_featured);
        $this->assertNull($helpDesk->github_url);
        $this->assertSame('https://helpdesk-ticketing.42web.io/', $helpDesk->live_url);
        $this->assertStringContainsString('secure Laravel support portal', $helpDesk->short_description);
        $this->assertEqualsCanonicalizing(
            ['Laravel'],
            $helpDesk->technologies()->pluck('name')->all(),
        );

        $this->assertSame(ProjectStatus::InDevelopment, $seawiseHub->status);
        $this->assertFalse($seawiseHub->is_published);
        $this->assertNull($seawiseHub->github_url);
        $this->assertNull($seawiseHub->live_url);
        $this->assertStringContainsString('plankton detection and classification', $seawiseHub->full_description);
        $this->assertEqualsCanonicalizing(
            ['AI Integration', 'Laravel', 'MySQL', 'PHP', 'Python'],
            $seawiseHub->technologies()->pluck('name')->all(),
        );

        $this->seed(PortfolioContentSeeder::class);

        $this->assertSame(3, Project::query()->count());
        $this->assertSame(2, Experience::query()->count());
        $this->assertSame(1, Education::query()->count());
    }

    public function test_deleting_a_project_cascades_images_and_pivot_rows(): void
    {
        $project = Project::factory()->create();
        $technology = Technology::factory()->create();
        $project->technologies()->attach($technology);
        $image = ProjectImage::query()->create([
            'project_id' => $project->id,
            'path' => 'projects/screenshots/delete.webp',
            'alt_text' => 'Delete',
            'display_order' => 0,
        ]);

        $project->delete();

        $this->assertDatabaseMissing('project_images', ['id' => $image->id]);
        $this->assertDatabaseMissing('project_technology', ['project_id' => $project->id]);
    }
}
