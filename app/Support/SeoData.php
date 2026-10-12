<?php

namespace App\Support;

use App\Models\Project;
use App\Models\SiteSetting;

readonly class SeoData
{
    public function __construct(public string $title, public string $description, public string $canonical, public ?string $image = null) {}

    public static function home(SiteSetting $settings): self
    {
        return new self($settings->seo_title ?: $settings->owner_name, $settings->seo_description ?: 'Developer portfolio.', route('home'));
    }

    public static function projects(SiteSetting $settings): self
    {
        return new self('Projects — '.$settings->owner_name, 'Published project gallery.', route('projects.index'));
    }

    public static function project(SiteSetting $settings, Project $project): self
    {
        return new self($project->seo_title ?: $project->title, $project->seo_description ?: $project->short_description, route('projects.show', $project), $project->main_image ? asset('storage/'.$project->main_image) : null);
    }
}
