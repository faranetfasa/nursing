<?php

namespace App\Modules\Core\Http\Middleware;

use App\Modules\Core\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/** Applies the locale and timezone stored in the settings table. */
class SetLocaleAndDirection
{
    public function __construct(private readonly SettingService $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $this->settings->get('general.locale', config('app.locale'));
        $timezone = (string) $this->settings->get('general.timezone', config('app.timezone'));

        App::setLocale($locale);
        date_default_timezone_set($timezone);

        view()->share('direction', in_array($locale, ['fa', 'ar', 'he'], true) ? 'rtl' : 'ltr');
        view()->share('locale', $locale);

        return $next($request);
    }
}
