<?php

namespace App\Observers;

use App\Models\Project;
use App\Services\ProjectImageService;

class ProjectObserver
{
    public function __construct(private readonly ProjectImageService $images) {}

    public function updated(Project $project): void
    {
        if ($project->wasChanged('main_image')) {
            $this->images->delete($project->getOriginal('main_image'));
        }
    }

    public function deleting(Project $project): void
    {
        $this->images->delete($project->main_image);
        $project->images()->pluck('path')->each(fn (string $path) => $this->images->delete($path));
    }
}
