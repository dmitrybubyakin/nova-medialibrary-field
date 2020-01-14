<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Requests;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Laravel\Nova\Http\Requests\NovaRequest;

class MedialibraryRequest extends NovaRequest
{
    public function medialibraryField(): Medialibrary
    {
        return $this
            ->newResource()
            ->availableFields($this)
            ->whereInstanceOf(Medialibrary::class)
            ->findFieldByAttribute($this->field);
    }

    public function resourceExists(): bool
    {
        return $this->route('resourceId') !== 'undefined';
    }

    public function fieldUuid(): string
    {
        return $this->fieldUuid;
    }
}
