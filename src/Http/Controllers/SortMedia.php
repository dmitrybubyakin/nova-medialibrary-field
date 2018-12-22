<?php

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\Models\Media;

class SortMedia
{
    public function __invoke(Request $request)
    {
        Media::setNewOrder($request->ids);
    }
}
