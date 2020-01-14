<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\Nova;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

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
                }),

            Medialibrary::make('Media testing', 'testing'),

            Medialibrary::make('Media testing single', 'testing_single')
                ->single(),

            Medialibrary::make('Media testing validation', 'testing_validation')
                ->attachRules('required', 'image'),
        ];
    }
}
