<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Files are stored on the private "secure" disk (outside public/) and streamed
 * through this controller only (specification item 133). Reading a file needs
 * the file permission; branding assets referenced by the layouts are the only
 * exception, they are readable by every authenticated user.
 */
class SecureFileController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    public function show(Request $request, string $path): StreamedResponse
    {
        $path = $this->validatePath($path);

        abort_unless(
            $this->isBrandingAsset($path) || $request->user()?->can(config('core.storage.permission', 'core.files.view')),
            403
        );

        $disk = Storage::disk(config('core.storage.disk', 'secure'));

        abort_unless($disk->exists($path), 404);

        return $disk->response($path);
    }

    /**
     * Whitelist validation instead of stripping traversal sequences: every
     * segment must be a plain file or directory name.
     */
    private function validatePath(string $path): string
    {
        $path = ltrim($path, '/');

        abort_if($path === '' || str_contains($path, '\\') || str_contains($path, "\0"), 404);

        foreach (explode('/', $path) as $segment) {
            abort_unless(preg_match('/^(?!\.{1,2}$)[\pL\pN._\-() ]+$/u', $segment) === 1, 404);
        }

        return $path;
    }

    private function isBrandingAsset(string $path): bool
    {
        $branding = $this->settings->branding();

        return in_array($path, array_filter([$branding['logo'] ?? null, $branding['favicon'] ?? null]), true);
    }
}
