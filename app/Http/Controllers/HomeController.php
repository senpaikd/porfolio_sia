<?php

namespace App\Http\Controllers;

use App\Support\SeoData;
use App\View\Models\HomePageData;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(HomePageData $data): View
    {
        $viewData = $data->toArray();
        $viewData['seo'] = SeoData::home($viewData['settings']);

        return view('home', $viewData);
    }
}
