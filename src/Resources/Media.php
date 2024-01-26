<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Resources;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\MedialibraryFieldResolver;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaModel;

class Media extends Resource
{
    public static string $model = MediaModel::class;

    public static $displayInNavigation = false;

    public static $globallySearchable = false;

    public static $trafficCop = false;

    public static function uriKey(): string
    {
        return 'dmitrybubyakin-nova-medialibrary-media';
    }

    public function fields(NovaRequest $request): array
    {
        $viaResource = $request->input('viaResource');
        $resource = Nova::resourceInstanceForKey($viaResource);
        $viaField = $request->input('viaField');

        $resolver = new MedialibraryFieldResolver($request, $resource, $viaField);

        /** @var Medialibrary $field */
        $field = call_user_func($resolver);

        if (is_null($field)) {
            return [];
        }

        return call_user_func(
            $field->fieldsCallback->bindTo($this),
            $request,
        );
    }
}
