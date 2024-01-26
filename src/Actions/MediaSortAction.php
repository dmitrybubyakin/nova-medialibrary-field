<?php

namespace DmitryBubyakin\NovaMedialibraryField\Actions;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaSortData;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaSortAction
{
    public function handle(MediaSortData $data): void
    {
        $this->changeOrder($data);
    }

    private function changeOrder(MediaSortData $data): void
    {
        /** @var Media $model */
        $model = config('media-library.media_model');

        $model::setNewOrder($data->ids);
    }
}