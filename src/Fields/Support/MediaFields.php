<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Support;

use DmitryBubyakin\NovaMedialibraryField\Fields\GeneratedConversions;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class MediaFields
{
    public static function make(): callable
    {
        return function (Request $request) {
            return [
                ID::make(),

                Text::make('Filename', 'file_name')
                    ->rules('required', 'min:2'),

                Textarea::make('Description', 'custom_properties->description')->alwaysShow(),

                Text::make('Disk')->exceptOnForms(),

                Text::make('Download Url', function () {
                    return $this->resource->exists ? $this->resource->getFullUrl() : null;
                }),

                Text::make('Size')->displayUsing(function () {
                    return $this->resource->humanReadableSize;
                })->exceptOnForms(),

                Text::make('Updated At')->displayUsing(function () {
                    return $this->resource->updated_at->diffForHumans();
                })->exceptOnForms(),

                GeneratedConversions::make('Conversions'),
            ];
        };
    }
}
