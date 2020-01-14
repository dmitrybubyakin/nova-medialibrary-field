<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Resources;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;

class Media extends Resource
{
    public static $model = 'Spatie\MediaLibrary\Models\Media';

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

        $field = $resource
                ->availableFields($request)
                ->whereInstanceOf(Medialibrary::class)
                ->findFieldByAttribute($request->input('viaField'));

        if (is_null($field)) {
            return [];
        }

        return call_user_func($field->fieldsCallback->bindTo($this), $request);
    }
}
