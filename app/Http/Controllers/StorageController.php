<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Serve a file from the public storage disk.
     * Use this when the storage symlink does not work (e.g. Windows + php artisan serve).
     */
    public function show(Request $request, string $path): BinaryFileResponse
    {
        $path = str_replace(['../', '..\\'], '', $path);

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($path);

        return response()->file($fullPath, [
            'Content-Type' => Storage::disk('public')->mimeType($path),
        ]);
    }
}
