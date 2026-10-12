<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        return response()->view('sitemap', ['projects' => Project::query()->published()->ordered()->get(['slug', 'updated_at'])])->header('Content-Type', 'application/xml');
    }
}
