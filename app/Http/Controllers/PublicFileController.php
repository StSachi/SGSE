
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class PublicFileController extends Controller
{
    public function show(string $path)
    {
        if (str_contains($path, '..')) {
            abort(400);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path);
    }
}
