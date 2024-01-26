<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Actions\MediaAttachAction;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MediaAttachRequest;
use Illuminate\Http\JsonResponse;

class MediaAttachController
{
    public function __invoke(
        MediaAttachRequest $request,
        MediaAttachAction $action,
    ): JsonResponse
    {
        $action->handle($request->getData());

        return response()->json(status: 201);
    }
}
