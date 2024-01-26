<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Actions\MediaListAction;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MediaListRequest;
use Illuminate\Http\JsonResponse;

class MediaListController
{
    public function __invoke(
        MediaListRequest $request,
        MediaListAction $action,
    ): JsonResponse
    {
        $media = $action->handle($request->getData());

        return response()->json($media);
    }
}
