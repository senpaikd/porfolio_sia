<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\SiteSetting;
use App\Support\SeoData;
use Illuminate\Contracts\View\View;

class CertificationController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::current();

        return view('certifications.index', [
            'settings' => $settings,
            'certifications' => Certification::query()->published()->ordered()->paginate(12),
            'seo' => new SeoData(
                'Certifications — '.$settings->owner_name,
                'Professional certifications and completed learning programs.',
                route('certifications.index'),
            ),
        ]);
    }
}
