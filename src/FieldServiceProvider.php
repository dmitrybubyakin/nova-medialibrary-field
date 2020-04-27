<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField;

use DmitryBubyakin\NovaMedialibraryField\Resources\Media;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../resources/lang/' => resource_path('lang/vendor/nova-medialibrary-field'),
        ]);

        $this->loadJSONTranslationsFrom(resource_path('lang/vendor/nova-medialibrary-field'));

        Nova::serving(function (ServingNova $event): void {
            Nova::script('nova-medialibrary-field', __DIR__.'/../dist/js/field.js');
            Nova::style('nova-medialibrary-field', __DIR__.'/../dist/css/field.css');
        });

        Media::$model = config('medialibrary.media_model');

        Nova::resources([
            Media::class,
        ]);

        $this->app->booted(function (): void {
            $this->routes();
            $this->translations();
        });
    }

    public function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/dmitrybubyakin/nova-medialibrary-field')
            ->group(function (): void {
                Route::post('sort', Http\Controllers\SortController::class);
                Route::post('{media}/crop', Http\Controllers\CropController::class);
                Route::post('{media}/regenerate', Http\Controllers\RegenerateController::class);
                Route::get('{resource}/{resourceId}/media/{field}', Http\Controllers\IndexController::class);
                Route::get('{resource}/{resourceId}/media/{field}/attachable', Http\Controllers\AttachableController::class);
                Route::post('{resource}/{resourceId}/media/{field}', Http\Controllers\AttachController::class);
            });
    }

    public function translations(): void
    {
        $locale = $this->app->getLocale();

        Nova::translations(__DIR__.'/../resources/lang/'.$locale.'.json');
        Nova::translations(resource_path('lang/vendor/nova-medialibrary-field/'.$locale.'.json'));
    }

    public function register(): void
    {
        //
    }
}
