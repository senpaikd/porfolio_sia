<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Certifications\Pages\CreateCertification;
use App\Models\Certification;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CertificationResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_administrator_can_open_certification_creation_page(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);

        $this->actingAs($administrator)
            ->get('/admin/certifications/create')
            ->assertOk();
    }

    public function test_administrator_can_create_certification_with_safely_named_pdf(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(CreateCertification::class)
            ->fillForm([
                'title' => 'AI Fundamentals',
                'issuer' => 'Cisco Networking Academy',
                'completion_date' => '2025-12-26',
                'certificate_path' => UploadedFile::fake()->create('my original certificate.pdf', 200, 'application/pdf'),
                'is_featured' => true,
                'is_published' => true,
                'display_order' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $certification = Certification::query()->where('title', 'AI Fundamentals')->firstOrFail();

        $this->assertStringStartsWith('certificates/', $certification->certificate_path);
        $this->assertNotSame('certificates/my original certificate.pdf', $certification->certificate_path);
        Storage::disk('public')->assertExists($certification->certificate_path);
    }

    public function test_certificate_upload_rejects_non_pdf_files(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(CreateCertification::class)
            ->fillForm([
                'title' => 'Invalid Certificate',
                'issuer' => 'Example Issuer',
                'certificate_path' => UploadedFile::fake()->image('certificate.jpg'),
            ])
            ->call('create')
            ->assertHasFormErrors(['certificate_path']);

        $this->assertDatabaseMissing('certifications', ['title' => 'Invalid Certificate']);
    }

    public function test_non_administrator_cannot_open_certification_creation_page(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->get('/admin/certifications/create')->assertForbidden();
    }

    public function test_replacing_a_certificate_removes_the_obsolete_pdf(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('certificates/old.pdf', 'old certificate');
        Storage::disk('public')->put('certificates/new.pdf', 'new certificate');

        $certification = Certification::factory()->create([
            'certificate_path' => 'certificates/old.pdf',
        ]);

        $certification->update(['certificate_path' => 'certificates/new.pdf']);

        Storage::disk('public')->assertMissing('certificates/old.pdf');
        Storage::disk('public')->assertExists('certificates/new.pdf');
    }

    public function test_deleting_a_certification_removes_its_pdf(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('certificates/certificate.pdf', 'certificate');

        $certification = Certification::factory()->create([
            'certificate_path' => 'certificates/certificate.pdf',
        ]);

        $certification->delete();

        Storage::disk('public')->assertMissing('certificates/certificate.pdf');
    }
}
