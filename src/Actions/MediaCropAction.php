<?php

namespace DmitryBubyakin\NovaMedialibraryField\Actions;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaCropData;
use Spatie\Image\Enums\Orientation;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaCropAction
{
    public function handle(MediaCropData $data): void
    {
        $media = $this->findMedia($data->mediaId);

        $this->setManipulations($media, $data);
    }

    private function findMedia(int $mediaId): Media
    {
        /** @var Media $media */
        $media = config('media-library.media_model');

        /** @var Media $media */
        $media = $media::query()->findOrFail($mediaId);

        return $media;
    }

    private function setManipulations(Media $media, MediaCropData $data): void
    {
        $media->manipulations = [
            $data->conversion => [
                'manualCrop' => $this->getManualCrop($data),
                //'orientation' => $this->getOrientation($data),
            ],
        ];

        $media->save();
    }

    private function getManualCrop(MediaCropData $data): array
    {
        return [
            $data->cropWidth,
            $data->cropHeight,
            $data->cropX,
            $data->cropY,
        ];
    }

    private function getOrientation(MediaCropData $data): Orientation
    {
        return match ($data->rotate) {
            90 => Orientation::Rotate90,
            180 => Orientation::Rotate180,
            270 => Orientation::Rotate270,
            default => Orientation::Rotate0,
        };
    }
}