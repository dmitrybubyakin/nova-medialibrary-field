<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Integrations\NovaDependencyContainer;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Laravel\Nova\Fields\FieldCollection;

class ResolveFromDependencyContainerFields
{
    public function __invoke(FieldCollection $fields, string $attribute): ?Medialibrary
    {
        $callback = function (mixed $field) {
            if (! is_a($field, \Epartment\NovaDependencyContainer\NovaDependencyContainer::class)) {
                return $field;
            }

            return $field->meta['fields'];
        };

        /** @var FieldCollection $fields */
        $fields = $fields
            ->map($callback)
            ->flatten()
            ->whereInstanceOf(Medialibrary::class);

        return $fields->findFieldByAttribute($attribute);
    }
}
