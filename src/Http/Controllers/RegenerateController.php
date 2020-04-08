<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\Conversions\FileManipulator;

class RegenerateController
{
    public function __invoke(Request $request, FileManipulator $fileManipulator): JsonResponse
    {
        $media = config('media-library.media_model')::findOrFail($request->route('media'));

        $fileManipulator->createDerivedFiles($media);

        return response()->json();
    }
}
