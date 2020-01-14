<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\Models\Media;

class SortController
{
    public function __invoke(Request $request): void
    {
        Media::setNewOrder($request->input('media', []));
    }
}
