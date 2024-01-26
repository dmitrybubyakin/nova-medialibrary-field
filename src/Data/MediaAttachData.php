<?php

namespace DmitryBubyakin\NovaMedialibraryField\Data;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;
use Spatie\MediaLibrary\HasMedia;

class MediaAttachData extends Data
{
    public function __construct(
        public Medialibrary $field,
        public ?string $fieldUuid,
        public ?HasMedia $resourceModel,
        public bool $resourceExists,
        public string $resource,
        public UploadedFile $file,
    ) {
    }
}