<?php

namespace DmitryBubyakin\NovaMedialibraryField;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use DmitryBubyakin\NovaMedialibraryField\Http\Controllers\SortMedia;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-medialibrary-field', __DIR__.'/../dist/js/field.js');
            Nova::style('nova-medialibrary-field', __DIR__.'/../dist/css/field.css');
        });

        $this->app->booted(function () {
            $this->routes();
        });
    }

    /**
     * Register the field's routes.
     *
     * @return void
     */
    public function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
                ->prefix('nova-vendor/dmitrybubyakin/nova-medialibrary-field')
                ->group(function () {
                    Route::post('sort', SortMedia::class);
                });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
