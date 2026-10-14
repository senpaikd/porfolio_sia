<?php

namespace Tests\Feature;

use App\Models\Certification;
use Database\Seeders\PortfolioContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificationPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_certifications_page_is_available(): void
    {
        $this->get('/certifications')
            ->assertOk()
            ->assertSee('Certifications')
            ->assertSee(route('certifications.index'), false);
    }

    public function test_certification_records_have_the_required_content_fields(): void
    {
        $this->assertTrue(Schema::hasColumns('certifications', [
            'title',
            'issuer',
            'completion_date',
            'certificate_path',
            'is_featured',
            'is_published',
            'display_order',
        ]));
    }

    public function test_certifications_page_only_lists_published_records_in_configured_order(): void
    {
        Storage::fake('public');

        Certification::factory()->create([
            'title' => 'Second Certificate',
            'certificate_path' => 'certificates/second.pdf',
            'display_order' => 20,
        ]);
        Certification::factory()->create([
            'title' => 'First Certificate',
            'certificate_path' => 'certificates/first.pdf',
            'display_order' => 1,
        ]);
        Certification::factory()->create([
            'title' => 'Private Certificate',
            'is_published' => false,
            'display_order' => 0,
        ]);

        $this->get('/certifications')
            ->assertOk()
            ->assertSeeInOrder(['First Certificate', 'Second Certificate'])
            ->assertDontSee('Private Certificate')
            ->assertSee('/storage/certificates/first.pdf', false);
    }

    public function test_homepage_shows_only_published_featured_certificates(): void
    {
        Certification::factory()->create(['title' => 'Featured Certificate', 'is_featured' => true]);
        Certification::factory()->create(['title' => 'Ordinary Certificate', 'is_featured' => false]);
        Certification::factory()->create(['title' => 'Draft Featured Certificate', 'is_featured' => true, 'is_published' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Featured Certificate')
            ->assertDontSee('Ordinary Certificate')
            ->assertDontSee('Draft Featured Certificate');
    }

    public function test_portfolio_seeder_imports_the_verified_certificates_and_pdfs(): void
    {
        Storage::fake('public');

        $this->seed(PortfolioContentSeeder::class);

        $this->assertDatabaseCount('certifications', 14);
        $this->assertSame(6, Certification::query()->where('is_featured', true)->count());
        $this->assertDatabaseHas('certifications', [
            'title' => 'AI Fundamentals with IBM SkillsBuild',
            'completion_date' => '2025-12-26 00:00:00',
            'certificate_path' => 'certificates/ai-fundamentals-with-ibm-skillsbuild.pdf',
        ]);
        Storage::disk('public')->assertExists('certificates/ai-fundamentals-with-ibm-skillsbuild.pdf');
    }
}
