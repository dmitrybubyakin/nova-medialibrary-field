<?php

namespace DmitryBubyakin\NovaMedialibraryField\Data;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Spatie\LaravelData\Data;
use Spatie\MediaLibrary\HasMedia;

class MediaListData extends Data
{
    public function __construct(
        public Medialibrary $field,
        public ?string $fieldUuid,
        public ?HasMedia $resourceModel,
        public bool $resourceExists,
    ) {
    }
}