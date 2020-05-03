<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Support;

use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class ResolveMediaCallback
{
    public function __invoke(HasMedia $model, string $collectionName): Collection
    {
        return $model->getMedia($collectionName);
    }
}
