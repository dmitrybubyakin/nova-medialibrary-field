<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Integrations\NovaFlexibleContent;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Whitecube\NovaFlexibleContent\Layouts\Layout;

/**
 * @mixin \Whitecube\NovaFlexibleContent\Layouts\Layout
 */
trait HasMedialibraryField
{
    protected function replaceTemporaryKeysWhenFilling(): void
    {
        if (is_null($this->_key) || is_null($this->key)) {
            return;
        }

        $medialibraryFields = $this->fields->whereInstanceOf(Medialibrary::class);

        $callback = function (NovaRequest $request, mixed $model): void {
            if ($model instanceof Layout) {
                /** @var Media $media */
                $media = config('media-library.media_model');

                $media::query()
                    ->where('custom_properties->flexibleKey', $model->_key)
                    ->update([
                        'custom_properties->flexibleKey' => $model->key,
                    ]);
            }
        };

        /** @var Medialibrary $field */
        foreach ($medialibraryFields as $field) {
            $field->fillUsing($callback);
        }
    }
}
