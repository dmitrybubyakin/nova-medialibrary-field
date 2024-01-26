<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Actions\MediaRegenerateAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaRegenerateController
{
    public function __invoke(
        Request $request,
        MediaRegenerateAction $action,
    ): JsonResponse
    {
        $action
            ->handle(
                (int) $request->route('media'),
            );

        return response()->json();
    }
}
