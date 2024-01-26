<?php

namespace DmitryBubyakin\NovaMedialibraryField\Actions;

use Spatie\MediaLibrary\Conversions\FileManipulator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaRegenerateAction
{
    private FileManipulator $fileManipulator;

    public function __construct(FileManipulator $fileManipulator)
    {
        $this->fileManipulator = $fileManipulator;
    }

    public function handle(int $mediaId): void
    {
        $media = $this->findMedia($mediaId);

        $this->fileManipulator->createDerivedFiles($media);
    }

    private function findMedia(int $mediaId): Media
    {
        /** @var Media $media */
        $media = config('media-library.media_model');

        /** @var Media $media */
        $media = $media::query()->findOrFail($mediaId);

        return $media;
    }
}