<?php

namespace DmitryBubyakin\NovaMedialibraryField\Data;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MediaAttachmentListRequest;
use Spatie\LaravelData\Data;
use Spatie\MediaLibrary\HasMedia;

class MediaAttachmentListData extends Data
{
    public function __construct(
        public MediaAttachmentListRequest $request,
        public Medialibrary $field,
        public ?HasMedia $resourceModel,
        public bool $resourceExists,
        public int $perPage = 25,
        public ?string $name = null,
        public ?int $maxSize = null,
        public ?string $mimeType = null,
    ) {
    }
}