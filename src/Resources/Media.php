<?php

namespace DmitryBubyakin\NovaMedialibraryField\Resources;

use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Spatie\MediaLibrary\Models\Media as MediaModel;

class Media extends Resource
{
    public static $model = MediaModel::class;

    public static $displayInNavigation = false;

    public static function singularLabel(): string
    {
        return 'Media';
    }

    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make('Filename', 'file_name'),

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
        ];
    }
}
