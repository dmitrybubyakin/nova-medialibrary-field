<?php

namespace DmitryBubyakin\NovaMedialibraryField\Data;

use Spatie\LaravelData\Data;

class MediaCropData extends Data
{
    public function __construct(
        public int $mediaId,
        public string $conversion,
        public int $cropWidth,
        public int $cropHeight,
        public int $cropX,
        public int $cropY,
        public int $rotate,
    ) {
    }
}