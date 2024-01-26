<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Actions\MediaAttachmentListAction;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MediaAttachmentListRequest;
use Illuminate\Http\JsonResponse;

class MediaAttachmentListController
{
    public function __invoke(
        MediaAttachmentListRequest $request,
        MediaAttachmentListAction $action,
    ): JsonResponse
    {
        $media = $action->handle($request->getData());

        return response()->json($media);
    }
}
