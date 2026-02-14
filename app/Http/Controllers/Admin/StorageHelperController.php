<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class StorageHelperController extends Controller
{
    /**
     * Serve file from storage/app/public directly via PHP.
     * This bypasses symlink issues on shared hosting.
     */
    public function show($path)
    {
        // Decode path in case it has slashes encoded
        $path = urldecode($path);

        // Security: Prevent directory traversal
        if (strpos($path, '..') !== false) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->path($path);

        // Determine mime type
        $mimeType = mime_content_type($file);

        return response()->file($file, ['Content-Type' => $mimeType]);
    }
}
