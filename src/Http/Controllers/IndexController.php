<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Fields\Support\MediaPresenter;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MedialibraryRequest;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\Models\Media;

class IndexController
{
    public function __invoke(MedialibraryRequest $request): JsonResponse
    {
        $field = $request->medialibraryField();

        $media = $request->resourceExists()
                ? $request->findResourceOrFail()->getMedia($field->collectionName)
                : TransientModel::make()->getMedia($request->fieldUuid());

        return response()->json(
            $media->map(function (Media $media) use ($field): MediaPresenter {
                return new MediaPresenter($media, $field);
            })
        );
    }
}
