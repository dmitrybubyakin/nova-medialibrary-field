<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Support;

use App\Nova\Resource;
use DmitryBubyakin\NovaMedialibraryField\Fields\GeneratedConversions;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaFields
{
    public static function make(): callable
    {
        return function () {
            /** @var Resource $this */

            /** @var Media $resource */
            $resource = $this->model();

            $mediaUrlCallback = function () use ($resource): ?string {
                return $resource->exists ? $resource->getFullUrl() : null;
            };

            $mediaSizeDisplayCallback = function () use ($resource): ?string {
                return $resource->humanReadableSize;
            };

            $updatedAtDisplayCallback = function () use ($resource): ?string {
                return $resource->updated_at->diffForHumans();
            };

            return [
                ID::make(),

                Text::make(__('Media Filename'), 'file_name')
                    ->rules('required', 'min:2'),

                Textarea::make(__('Media Description'), 'custom_properties->description')
                    ->alwaysShow(),

                Text::make(__('Media Disk'))
                    ->exceptOnForms(),

                Text::make(__('Media Download Url'), $mediaUrlCallback),

                Text::make(__('Media Size'))
                    ->displayUsing($mediaSizeDisplayCallback)
                    ->exceptOnForms(),

                Text::make(__('Media Updated At'))
                    ->displayUsing($updatedAtDisplayCallback)
                    ->exceptOnForms(),

                GeneratedConversions::make(__('Media Conversions')),
            ];
        };
    }
}
