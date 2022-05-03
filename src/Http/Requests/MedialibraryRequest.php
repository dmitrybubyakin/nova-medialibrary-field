<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Requests;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\MedialibraryFieldResolver;
use Laravel\Nova\Http\Requests\NovaRequest;

class MedialibraryRequest extends NovaRequest
{
    public function medialibraryField(): Medialibrary
    {
        return call_user_func(new MedialibraryFieldResolver($this));
    }

    public function resourceExists(): bool
    {
        return is_numeric($this->route('resourceId'));
    }

    public function fieldUuid(): string
    {
        return $this->input('fieldUuid');
    }
}
