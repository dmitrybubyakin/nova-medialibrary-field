<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\Integrations\NovaDependencyContainer\ResolveFromDependencyContainerFields;
use DmitryBubyakin\NovaMedialibraryField\Integrations\NovaFlexibleContent\ResolveFromFlexibleLayoutFields;
use Exception;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class MedialibraryFieldResolver
{
    public static $resolvers = [
        ResolveFromFlexibleLayoutFields::class,
        ResolveFromDependencyContainerFields::class,
    ];

    private $request;
    private $resource;
    private $attribute;

    public function __construct(NovaRequest $request, Resource $resource = null, string $attribute = null)
    {
        $this->request = $request;
        $this->resource = $resource ?: $request->newResource();
        $this->attribute = $attribute ?: $request->field;
    }

    public function __invoke(): Medialibrary
    {
        $fields = $this->resource->availableFields($this->request);

        foreach (static::$resolvers as $className) {
            $resolver = new $className;

            if ($field = $resolver($fields, $this->attribute)) {
                return $field;
            }
        }

        return $fields
            ->whereInstanceOf(Medialibrary::class)
            ->findFieldByAttribute($this->attribute, function (): void {
                throw new Exception("Field with attribute `{$this->attribute}` is not found.");
            });
    }
}
