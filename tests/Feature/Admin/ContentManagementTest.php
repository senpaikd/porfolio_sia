<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Filament\Resources\Technologies\Pages\CreateTechnology;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_administrator_can_create_reusable_technology(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(CreateTechnology::class)
            ->fillForm(['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'Backend', 'is_published' => true, 'display_order' => 1])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('technologies', ['slug' => 'laravel']);
    }

    public function test_site_settings_remain_a_single_editable_record(): void
    {
        $settings = SiteSetting::current();

        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->assertSame($settings->id, SiteSetting::current()->id);
        $this->assertDatabaseCount('site_settings', 1);
        $this->assertFalse(SiteSettingResource::canDelete($settings));
        $this->assertFalse(SiteSettingResource::canDeleteAny());
    }

    public function test_contact_messages_are_not_creatable_or_editable_from_admin(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $message = ContactMessage::factory()->create();

        $this->assertFalse(ContactMessageResource::canCreate());
        $this->assertFalse(ContactMessageResource::canEdit($message));
    }

    public function test_non_administrator_cannot_manage_content(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->get('/admin/technologies')->assertForbidden();
    }
}
