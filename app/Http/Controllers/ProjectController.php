<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SiteSetting;
use App\Support\SeoData;
use Illuminate\Contracts\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::current();

        return view('projects.index', [
            'settings' => $settings,
            'projects' => Project::query()->published()->ordered()->with('technologies')->paginate(12),
            'seo' => SeoData::projects($settings),
        ]);
    }

    public function show(string $project): View
    {
        $project = Project::query()
            ->published()
            ->where('slug', $project)
            ->with(['technologies', 'images'])
            ->firstOrFail();

        $settings = SiteSetting::current();

        return view('projects.show', ['settings' => $settings, 'project' => $project, 'seo' => SeoData::project($settings, $project)]);
    }
}
