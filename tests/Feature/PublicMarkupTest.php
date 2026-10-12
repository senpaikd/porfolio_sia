<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicMarkupTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_has_accessible_landmarks_navigation_and_theme_control(): void
    {
        SiteSetting::factory()->create([
            'github_url' => 'https://github.com/example',
            'linkedin_url' => 'https://linkedin.com/in/example',
        ]);

        $response = $this->get(route('home'))->assertOk();

        $response->assertSee('<header', false)
            ->assertSee('<nav', false)
            ->assertSee('<main', false)
            ->assertSee('<footer', false)
            ->assertSee('aria-label="Primary navigation"', false)
            ->assertSee('id="theme-toggle"', false)
            ->assertSee('id="contact"', false)
            ->assertSee('rel="noopener noreferrer"', false);

        $this->assertSame(1, substr_count($response->getContent(), '<h1'));
    }

    public function test_homepage_exposes_the_progressive_3d_design_hooks(): void
    {
        Project::factory()->create(['is_featured' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="ambient-scene"', false)
            ->assertSee('data-typewriter', false)
            ->assertSee('aria-label="Technology highlights"', false)
            ->assertSee('data-tilt-card', false)
            ->assertSee('class="hero-shell"', false);
    }

    public function test_primary_navigation_uses_home_link_instead_of_initials_logo(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $response
            ->assertDontSee('class="font-extrabold tracking-tight"', false)
            ->assertSeeInOrder(['>Home</a>', '>About</a>'], false);
    }

    public function test_featured_project_card_and_project_image_have_useful_markup(): void
    {
        $project = Project::factory()->create([
            'title' => 'Accessible Project',
            'main_image' => 'projects/main/example.webp',
            'is_featured' => true,
            'github_url' => 'https://github.com/example/project',
        ]);

        $this->get(route('home'))
            ->assertSee('Accessible Project')
            ->assertSee('loading="lazy"', false)
            ->assertSee('alt="Accessible Project project preview"', false);

        $this->get(route('projects.show', $project))
            ->assertSee('Back to all projects')
            ->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_empty_featured_and_gallery_states_are_useful(): void
    {
        $this->get(route('home'))->assertSee('No featured projects are published yet.');
        $this->get(route('projects.index'))->assertSee('No projects are published yet.');
    }

    public function test_experience_timeline_renders_dates_and_responsibilities_as_a_list(): void
    {
        Experience::factory()->create([
            'company' => 'Example Studio',
            'role' => 'Front-End Developer',
            'start_date' => '2026-09-01',
            'end_date' => '2026-10-31',
            'description' => "Contributed to an existing codebase under a confidentiality agreement.\n- Refined responsive UI sections.\n- Resolved layout issues.",
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<time datetime="2026-09-01">Sep 2026</time>', false)
            ->assertSee('<time datetime="2026-10-31">Oct 2026</time>', false)
            ->assertSee('Contributed to an existing codebase under a confidentiality agreement.')
            ->assertSee('<ul class="timeline-responsibilities">', false)
            ->assertSee('<li>Refined responsive UI sections.</li>', false)
            ->assertSee('<li>Resolved layout issues.</li>', false);
    }
}
