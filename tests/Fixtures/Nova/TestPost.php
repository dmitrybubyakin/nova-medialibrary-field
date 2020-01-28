<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\Nova;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class TestPost extends Resource
{
    public static $model = 'DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\TestPost';

    public function fields(Request $request)
    {
        return [
            Medialibrary::make('Media')
                ->fields(function ($request) {
                    return [
                        Text::make('File Name')
                            ->onlyOnForms(),

                        Text::make('Disk')
                            ->onlyOnDetail(),
                    ];
                })
                ->attachExisting(function (Builder $query, Request $request, HasMedia $model) {
                    if ($request->name) {
                        return $query->where('name', $request->name);
                    }
                }),

            Medialibrary::make('Media testing', 'testing')
                ->rules('required', 'array')
                ->creationRules('min:1')
                ->updateRules('min:2'),

            Medialibrary::make('Media testing single', 'testing_single')
                ->single(),

            Medialibrary::make('Media testing validation', 'testing_validation')
                ->attachRules('required', 'image'),

            new Panel('Panel', [
                Medialibrary::make('Media testing panel', 'testing_panel'),
            ]),

            ContainerField::make('Container', [
                Medialibrary::make('Media testing container', 'testing_container'),
            ]),
        ];
    }
}
