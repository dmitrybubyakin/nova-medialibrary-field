<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Bridge\Flexible;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Whitecube\NovaFlexibleContent\Flexible;
use Whitecube\NovaFlexibleContent\Layouts\Layout;

class FlexibleMedialibraryFieldResolver
{
    public function __invoke(NovaRequest $request, Resource $resource, string $attribute): Medialibrary
    {
        $key = Str::before($attribute, '__');
        $attribute = Str::after($attribute, '__');

        return $resource
            ->availableFields($request)
            ->map(function ($field) use ($key) {
                if (! $field instanceof Flexible) {
                    return $field;
                }

                return $field->meta['layouts']->map(function (Layout $layout) use ($key) {
                    return collect($layout->fields())
                        ->whereInstanceOf(Medialibrary::class)
                        ->each(function (Medialibrary $field) use ($key): void {
                            $this->resolveUsing($field, $key);
                            $this->attachUsing($field, $key);
                        });
                })->collapse();
            })
            ->flatten()
            ->whereInstanceOf(Medialibrary::class)
            ->findFieldByAttribute($attribute);
    }

    public function resolveUsing(Medialibrary $field, string $key): void
    {
        $field->resolveMediaUsing(function (HasMedia $model, string $collectionName) use ($key) {
            return $model
                ->getMedia($collectionName, ['flexibleKey' => $key])
                ->values();
        });
    }

    public function attachUsing(Medialibrary $field, string $key): void
    {
        $field->attachUsing(function ($model, $file, $collectionName, $diskName, $fieldUuid) use ($key) {
            if ($model instanceof TransientModel) {
                $collectionName = $fieldUuid;
            }

            return $model
                ->addMedia($file)
                ->withCustomProperties(['flexibleKey' => $key])
                ->toMediaCollection($collectionName, $diskName);
        });
    }
}
