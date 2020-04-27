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

                Text::make(__('Media Filename'), 'file_name')
                    ->rules('required', 'min:2'),

                Textarea::make(__('Media Description'), 'custom_properties->description')->alwaysShow(),

                Text::make(__('Media Disk'))->exceptOnForms(),

                Text::make(__('Media Download Url'), function () {
                    return $this->resource->exists ? $this->resource->getFullUrl() : null;
                }),

                Text::make(__('Media Size'))->displayUsing(function () {
                    return $this->resource->humanReadableSize;
                })->exceptOnForms(),

                Text::make(__('Media Updated At'))->displayUsing(function () {
                    return $this->resource->updated_at->diffForHumans();
                })->exceptOnForms(),

                GeneratedConversions::make('Conversions'),
            ];
        };
    }
}
