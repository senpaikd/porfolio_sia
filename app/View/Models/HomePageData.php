<?php

namespace App\View\Models;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Technology;

class HomePageData
{
    public function toArray(): array
    {
        return [
            'settings' => SiteSetting::current(),
            'featuredProjects' => Project::query()->featured()->ordered()->with('technologies')->limit(6)->get(),
            'skills' => Skill::query()->where('is_published', true)->orderBy('display_order')->get(),
            'technologies' => Technology::query()->where('is_published', true)->orderBy('display_order')->get(),
            'experiences' => Experience::query()->where('is_published', true)->orderBy('display_order')->get(),
            'education' => Education::query()->where('is_published', true)->orderBy('display_order')->get(),
        ];
    }
}
