<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Exception;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class MedialibraryFieldResolver
{
    private static $callback;

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
        return call_user_func_array(static::$callback ?? $this->getDefaultResolver(), [
            $this->request,
            $this->resource,
            $this->attribute,
        ]);
    }

    private function getDefaultResolver(): callable
    {
        return function (NovaRequest $request, Resource $resource, string $attribute) {
            $field = $resource
                    ->availableFields($request)
                    ->whereInstanceOf(Medialibrary::class)
                    ->findFieldByAttribute($attribute);

            if (is_null($field)) {
                throw new Exception(sprintf(
                    'Field `%s` is not found. In case you are using Panel or Field which contains Medialibrary field, check out %s::using() method',
                    $attribute,
                    static::class
                ));
            }

            return $field;
        };
    }

    public static function using(callable $callback): void
    {
        static::$callback = $callback;
    }
}
