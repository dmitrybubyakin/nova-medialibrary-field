<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Actions\MediaSortAction;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MediaSortRequest;
use Illuminate\Http\JsonResponse;

class MediaSortController
{
    public function __invoke(
        MediaSortRequest $request,
        MediaSortAction $action,
    ): JsonResponse
    {
        $action->handle($request->getData());

        return response()->json();
    }
}
