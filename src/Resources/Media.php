<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Resources;

use DmitryBubyakin\NovaMedialibraryField\MedialibraryFieldResolver;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;

class Media extends Resource
{
    public static $model = \Spatie\MediaLibrary\MediaCollections\Models\Media::class;

    public static $displayInNavigation = false;

    public static $globallySearchable = false;

    public static $trafficCop = false;

    public static function uriKey(): string
    {
        return 'dmitrybubyakin-nova-medialibrary-media';
    }

    public function fields(Request $request): array
    {
        $resource = Nova::resourceInstanceForKey($request->input('viaResource'));

        $field = call_user_func(new MedialibraryFieldResolver(
            $request,
            $resource,
            $request->input('viaField')
        ));

        if (is_null($field)) {
            return [];
        }

        return call_user_func($field->fieldsCallback->bindTo($this), $request);
    }
}
