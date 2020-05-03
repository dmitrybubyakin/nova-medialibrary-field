<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Integrations\NovaFlexibleContent;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\FieldCollection;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class ResolveFromFlexibleLayoutFields
{
    public function __invoke(FieldCollection $fields, string $attribute): ?Medialibrary
    {
        $key = Str::before($attribute, '__');
        $attribute = Str::after($attribute, '__');

        return $fields->map(function ($field) use ($key) {
            if (! is_a($field, \Whitecube\NovaFlexibleContent\Flexible::class)) {
                return $field;
            }

            return $field->meta['layouts']->map(function (\Whitecube\NovaFlexibleContent\Layouts\Layout $layout) use ($key) {
                return $this->setupLayoutFields($layout, $key);
            })->collapse();
        })
            ->flatten()
            ->whereInstanceOf(Medialibrary::class)
            ->findFieldByAttribute($attribute);
    }

    public function setupLayoutFields(\Whitecube\NovaFlexibleContent\Layouts\Layout $layout, string $key): Collection
    {
        return collect($layout->fields())
            ->whereInstanceOf(Medialibrary::class)
            ->each(function (Medialibrary $field) use ($key): void {
                $this->resolveUsing($field, $key);
                $this->attachUsing($field, $key);
            });
    }

    public function resolveUsing(Medialibrary $field, string $key): void
    {
        $field->resolveMediaUsing(function (HasMedia $model, string $collectionName) use ($key) {
            return $model
                ->getMedia($collectionName, ['flexibleKey' => $key])
                ->values();
        });
    }

    public function attachUsing(Medialibrary $field, string $key): void
    {
        $field->attachUsing(function ($model, $file, $collectionName, $diskName, $fieldUuid) use ($key) {
            if ($model instanceof TransientModel) {
                $collectionName = $fieldUuid;
            }

            return $model
                ->addMedia($file)
                ->withCustomProperties(['flexibleKey' => $key])
                ->toMediaCollection($collectionName, $diskName);
        });
    }
}
