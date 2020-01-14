<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MedialibraryRequest;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\Models\Media;

class AttachController
{
    public function __invoke(MedialibraryRequest $request): JsonResponse
    {
        [$model, $uuid] = $request->resourceExists()
                        ? [$request->findModelOrFail(), '']
                        : [TransientModel::make(), $request->fieldUuid()];

        $field = $request->medialibraryField();

        $request->validate(['file' => $field->getAttachRules($request)]);

        $this->rememberTargetModel($request, $model, $field->collectionName);

        $media = call_user_func_array($field->attachCallback, [
            $model,
            $request->file,
            $field->collectionName,
            $field->diskName,
            $uuid,
        ]);

        return response()->json([], 201);
    }

    private function rememberTargetModel(MedialibraryRequest $request, HasMedia $model, string $collectionName): void
    {
        if (! $model instanceof TransientModel) {
            return;
        }

        Media::creating(function (Media $media) use ($request, $model, $collectionName): void {
            if ($model instanceof TransientModel && $media->collection_name === $request->fieldUuid()) {
                TransientModel::setCustomPropertyValue(
                    $media,
                    $request->resource()::$model,
                    $collectionName,
                );
            }
        });
    }
}
