<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField;

use DmitryBubyakin\NovaMedialibraryField\Resources\Media;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishLang();
        $this->publishNovaResources();

        $this->app->booted(function (): void {
            $this->routes();
            $this->translations();
        });
    }

    private function publishLang(): void
    {
        $this->publishes([
           '/../resources/lang/' => resource_path('lang/vendor/nova-medialibrary-field'),
        ]);

        $this->loadJSONTranslationsFrom(resource_path('lang/vendor/nova-medialibrary-field'));
    }

    private function publishNovaResources(): void
    {
        Nova::serving(function (): void {
            Nova::script('nova-medialibrary-field', __DIR__ . '/../dist/js/field.js');
            Nova::style('nova-medialibrary-field', __DIR__ . '/../dist/css/field.css');

            Media::$model = config('media-library.media_model');

            Nova::resources([Media::class]);
        });
    }

    public function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/dmitrybubyakin/nova-medialibrary-field')
            ->group(__DIR__ . '/../routes/api.php');
    }

    public function translations(): void
    {
        $locale = $this->app->getLocale();

        Nova::translations(__DIR__ . '/../resources/lang/' . $locale . '.json');
        Nova::translations(resource_path('lang/vendor/nova-medialibrary-field/' . $locale . '.json'));
    }

    public function register(): void
    {
        //
    }
}