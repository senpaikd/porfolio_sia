<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use InvalidArgumentException;

class ProjectImageService
{
    public function store(UploadedFile $file, string $directory): string
    {
        $maximum = config("image.max_dimensions.{$directory}");
        if (! is_int($maximum)) {
            throw new InvalidArgumentException('The project media directory is not allowed.');
        }

        $encoded = ImageManager::gd()
            ->read($file->getRealPath())
            ->orient()
            ->scaleDown(width: $maximum, height: $maximum)
            ->toWebp(quality: (int) config('image.quality', 82), strip: true);

        $path = $directory.'/'.Str::uuid().'.webp';
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path === null || ! $this->isOwnedPath($path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function isOwnedPath(string $path): bool
    {
        if (str_contains($path, '..') || str_starts_with($path, '/')) {
            return false;
        }

        return str_starts_with($path, 'projects/main/') || str_starts_with($path, 'projects/screenshots/');
    }
}
