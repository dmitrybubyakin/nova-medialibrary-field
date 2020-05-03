<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Fields\Support\MediaPresenter;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MedialibraryRequest;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class IndexController
{
    public function __invoke(MedialibraryRequest $request): JsonResponse
    {
        $field = $request->medialibraryField();

        [$model, $collectionName] = $request->resourceExists()
            ? [$request->findModelOrFail(), $field->collectionName]
            : [TransientModel::make(), $request->fieldUuid()];

        $media = call_user_func($field->resolveMediaUsingCallback, $model, $collectionName);

        return response()->json(
            collect($media)->map(function (Media $media) use ($field): MediaPresenter {
                return new MediaPresenter($media, $field);
            })
        );
    }
}
