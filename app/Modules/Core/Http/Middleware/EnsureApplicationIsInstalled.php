<?php

namespace App\Modules\Core\Http\Middleware;

use App\Modules\Core\Services\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends the visitor to the graphical installer until storage/installed.lock
 * exists (specification items 6 and 13).
 */
class EnsureApplicationIsInstalled
{
    public function __construct(private readonly InstallerService $installer) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->installer->isInstalled() || ! $this->installer->installerEnabled()) {
            return $next($request);
        }

        $url = $this->installer->installerUrl();

        if ($request->expectsJson()) {
            abort(503, 'برنامه هنوز نصب نشده است. مسیر نصب: '.$url);
        }

        if ($request->is(ltrim($url, '/'))) {
            return $next($request);
        }

        return redirect()->to($url);
    }
}
