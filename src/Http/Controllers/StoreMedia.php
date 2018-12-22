<?php

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class StoreMedia
{
    public function __invoke(NovaRequest $request)
    {
        $this->authorizeToCreate($request);

        $parent = $request->findParentModelOrFail();

        $field = $this->resolveMedialibraryField($request, $parent);

        $request->validate(['file' => $field->rules]);

        return DB::transaction(function () use ($parent, $field) {
            $fileAdder = $parent->addMediaFromRequest('file');
            $collection = $field->collectionName;

            $oldFile = $field->multiple ? null : $parent->getFirstMedia($collection);

            if ($oldFile && is_callable($field->replaceUsingCallback)) {
                $fileAdder = call_user_func($field->replaceUsingCallback, $fileAdder, $oldFile);
            } elseif (is_callable($field->storeUsingCallback)) {
                $fileAdder = call_user_func($field->storeUsingCallback, $fileAdder);
            }

            return $field->serializeMedia($fileAdder->toMediaCollection($collection));
        });
    }

    public function authorizeToCreate(NovaRequest $request)
    {
        $resource = Nova::resourceForKey($request->resource);

        abort_unless($resource, 404);
        abort_unless($resource::authorizedToCreate($request), 403);
    }

    public function resolveMedialibraryField(NovaRequest $request, HasMedia $parent): Medialibrary
    {
        $parentResource = Nova::resourceForKey($request->viaResource);

        $fields = collect((new $parentResource($parent))->fields($request));

        return $fields->first(function (Field $field) use ($request) {
            return $field instanceof Medialibrary && $field->collectionName == $request->collection;
        });
    }
}
