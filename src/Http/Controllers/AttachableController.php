<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use function DmitryBubyakin\NovaMedialibraryField\call_or_default;
use DmitryBubyakin\NovaMedialibraryField\Fields\Support\AttachableMediaPresenter;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MedialibraryRequest;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class AttachableController
{
    public function __invoke(MedialibraryRequest $request): JsonResponse
    {
        $field = $request->medialibraryField();

        $model = $request->resourceExists() ? $request->findModelOrFail() : TransientModel::make();

        $query = $this->buildQuery($request, $model);

        $query = call_or_default($field->attachExistingCallback, [$query, $request, $model]) ?: $query;

        $paginator = $query->paginate($request->input('perPage', 25));

        return response()->json(
            $paginator->setCollection(
                $paginator->getCollection()->map( function ($media) use ($field) {
                    return new AttachableMediaPresenter($media, $field);
                })
            )
        );
    }

    private function buildQuery(MedialibraryRequest $request): Builder
    {
        return config('media-library.media_model')::query()->when($request->input('name'), function (Builder $query, string $name): void {
            $query->where(function (Builder $query) use ($name): void {
                $query
                    ->where('name', 'like', "%{$name}%")
                    ->orWhere('file_name', 'like', "%{$name}%");
            });
        })->when($request->input('maxSize'), function (Builder $query, int $maxSize): void {
            $query->where('size', '<=', $maxSize);
        })->when($request->input('mimeType'), function (Builder $query, string $mimeType): void {
            if (strpos($mimeType, ',') !== false) {
                $query->whereIn('mime_type', explode(',', $mimeType));
            } else {
                $query->where('mime_type', 'like', str_replace('*', '%', $mimeType));
            }
        });
    }
}
