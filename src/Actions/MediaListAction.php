<?php

namespace DmitryBubyakin\NovaMedialibraryField\Actions;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaListData;
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\Fields\Support\MediaPresenter;
use DmitryBubyakin\NovaMedialibraryField\Models\TransientModel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaListAction
{
    public function handle(MediaListData $data): array
    {
        $resourceModel = $this->getResourceModel($data);
        $collectionName = $this->getCollectionName($data);

        $media = $this->getMedia($data->field, $resourceModel, $collectionName);

        return $this->transformMedia($media, $data->field);
    }

    private function getResourceModel(MediaListData $data): HasMedia
    {
        return $data->resourceExists ? $data->resourceModel : TransientModel::make();
    }

    private function getCollectionName(MediaListData $data): ?string
    {
        return $data->resourceExists ? $data->field->collectionName : $data->fieldUuid;
    }

    private function getMedia(
        Medialibrary $field,
        HasMedia $resourceModel,
        string $collectionName,
    ): MediaCollection
    {
        return call_user_func(
            $field->resolveMediaUsingCallback,
            $resourceModel,
            $collectionName,
        );
    }

    private function transformMedia(MediaCollection $media, Medialibrary $field): array
    {
        return $media
            ->map(fn (Media $media): MediaPresenter => new MediaPresenter($media, $field))
            ->toArray();
    }
}