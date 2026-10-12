<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Observers\ProjectImageObserver;
use App\Observers\ProjectObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Project::observe(ProjectObserver::class);
        ProjectImage::observe(ProjectImageObserver::class);

        RateLimiter::for('contact', function (Request $request): Limit {
            $email = mb_strtolower(trim((string) $request->input('email')));

            return Limit::perMinute(3)->by(hash('sha256', $email.'|'.$request->ip()));
        });
    }
}
