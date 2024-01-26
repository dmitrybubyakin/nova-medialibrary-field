<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Actions\MediaCropAction;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MediaCropRequest;
use Illuminate\Http\JsonResponse;

class MediaCropController
{
    public function __invoke(
        MediaCropRequest $request,
        MediaCropAction $action,
    ): JsonResponse
    {
        $action->handle($request->getData());

        return response()->json();
    }
}
