<?php

namespace DmitryBubyakin\NovaMedialibraryField\Data;

use Spatie\LaravelData\Data;

class MediaSortData extends Data
{
    public function __construct(
        public array $ids,
    ) {
    }
}