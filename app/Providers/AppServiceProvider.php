<?php

namespace App\Providers;

use App\Support\ErrorReporter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ErrorReporter::class);
    }

    public function boot(): void
    {
        /*
         * Only an https APP_URL forces https links: an installation served over
         * plain http (the usual XAMPP setup) would otherwise generate
         * unreachable asset and form URLs.
         */
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
