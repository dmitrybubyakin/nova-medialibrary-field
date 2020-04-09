<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MedialibraryRequest;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class AttachController
{
    public function __invoke(MedialibraryRequest $request): JsonResponse
    {
        [$model, $uuid] = $request->resourceExists()
                        ? [$request->findModelOrFail(), '']
                        : [TransientModel::make(), $request->fieldUuid()];

        $field = $request->medialibraryField();

        if ($request->has('media')) {
            $this->replaceFile($request);
        }

        $request->validate(['file' => $field->getAttachRules($request)]);

        $this->rememberTargetModel($request, $model, $field->collectionName);

        call_user_func_array($field->attachCallback, [
            $model,
            $request->file,
            $field->collectionName,
            $field->diskName,
            $uuid,
        ]);

        return response()->json([], 201);
    }

    private function replaceFile(MedialibraryRequest $request): void
    {
        $media = config('media-library.media_model')::findOrFail($request->media);

        $directory = TemporaryDirectory::create();

        $temporaryFile = $directory->path('/').DIRECTORY_SEPARATOR.$media->file_name;

        app(Filesystem::class)->copyFromMediaLibrary($media, $temporaryFile);

        $request->merge([
            'file' => new UploadedFile($temporaryFile, $media->file_name, $media->mime_type, null, true),
        ]);
    }

    private function rememberTargetModel(MedialibraryRequest $request, HasMedia $model, string $collectionName): void
    {
        if (! $model instanceof TransientModel) {
            return;
        }

        config('media-library.media_model')::creating(function (Media $media) use ($request, $model, $collectionName): void {
            if ($model instanceof TransientModel && $media->collection_name === $request->fieldUuid()) {
                TransientModel::setCustomPropertyValue(
                    $media,
                    $request->resource()::$model,
                    $collectionName
                );
            }
        });
    }
}
