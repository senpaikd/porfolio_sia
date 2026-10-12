<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Services\ProjectImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectImageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploaded_images_are_stored_as_generated_bounded_webp_files(): void
    {
        Storage::fake('public');
        $path = app(ProjectImageService::class)->store(UploadedFile::fake()->image('unsafe name.jpg', 2400, 1800), 'projects/main');

        Storage::disk('public')->assertExists($path);
        $this->assertMatchesRegularExpression('#^projects/main/[0-9a-f-]+\.webp$#', $path);
        $size = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertLessThanOrEqual(1600, max($size[0], $size[1]));
        $this->assertSame('image/webp', $size['mime']);
    }

    public function test_replacing_and_deleting_project_media_cleans_only_owned_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/main/old.webp', 'old');
        Storage::disk('public')->put('projects/main/new.webp', 'new');
        Storage::disk('public')->put('other/keep.webp', 'keep');

        $project = Project::factory()->create(['main_image' => 'projects/main/old.webp']);
        $project->update(['main_image' => 'projects/main/new.webp']);

        Storage::disk('public')->assertMissing('projects/main/old.webp');
        Storage::disk('public')->assertExists('projects/main/new.webp');

        $image = ProjectImage::query()->create(['project_id' => $project->id, 'path' => 'projects/screenshots/delete.webp']);
        Storage::disk('public')->put($image->path, 'screenshot');
        $image->delete();

        Storage::disk('public')->assertMissing('projects/screenshots/delete.webp');
        app(ProjectImageService::class)->delete('other/keep.webp');
        Storage::disk('public')->assertExists('other/keep.webp');

        $project->delete();
        Storage::disk('public')->assertMissing('projects/main/new.webp');
    }

    public function test_deleting_a_missing_or_null_path_is_a_safe_noop(): void
    {
        Storage::fake('public');

        app(ProjectImageService::class)->delete(null);
        app(ProjectImageService::class)->delete('projects/main/missing.webp');

        $this->assertTrue(true);
    }
}
