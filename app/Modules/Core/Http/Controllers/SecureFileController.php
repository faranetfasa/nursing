<?php

namespace App\Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Files are stored on the private "secure" disk (outside public/) and streamed
 * through this controller only (specification item 133).
 */
class SecureFileController extends Controller
{
    public function show(Request $request, string $path): StreamedResponse
    {
        $path = ltrim(str_replace('..', '', $path), '/');
        $disk = Storage::disk(config('core.storage.disk', 'secure'));

        abort_unless($disk->exists($path), 404);

        return $disk->response($path);
    }
}
