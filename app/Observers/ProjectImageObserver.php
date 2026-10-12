<?php

namespace App\Observers;

use App\Models\ProjectImage;
use App\Services\ProjectImageService;

class ProjectImageObserver
{
    public function __construct(private readonly ProjectImageService $images) {}

    public function updated(ProjectImage $image): void
    {
        if ($image->wasChanged('path')) {
            $this->images->delete($image->getOriginal('path'));
        }
    }

    public function deleted(ProjectImage $image): void
    {
        $this->images->delete($image->path);
    }
}
