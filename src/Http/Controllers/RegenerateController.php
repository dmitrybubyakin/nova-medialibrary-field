<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\FileManipulator;

class RegenerateController
{
    public function __invoke(Request $request, FileManipulator $fileManipulator): JsonResponse
    {
        $media = config('medialibrary.media_model')::findOrFail($request->route('media'));

        $fileManipulator->createDerivedFiles($media);

        return response()->json();
    }
}
