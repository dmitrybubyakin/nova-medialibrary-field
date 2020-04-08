<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use Illuminate\Http\Request;

class SortController
{
    public function __invoke(Request $request): void
    {
        config('media-library.media_model')::setNewOrder($request->input('media', []));
    }
}
