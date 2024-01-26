<?php

namespace DmitryBubyakin\NovaMedialibraryField\Actions;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaAttachData;
use DmitryBubyakin\NovaMedialibraryField\Models\TransientModel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaAttachAction
{
    public function handle(MediaAttachData $data): void
    {
        $resourceModel = $this->getResourceModel($data);
        $uuid = $this->getUuid($data);

        $this->rememberTargetModel($data, $resourceModel);

        call_user_func_array($data->field->attachCallback, [
            $resourceModel,
            $data->file,
            $data->field->collectionName,
            $data->field->diskName,
            $uuid,
        ]);
    }

    private function getResourceModel(MediaAttachData $data): ?HasMedia
    {
        return $data->resourceExists ? $data->resourceModel : TransientModel::make();
    }

    private function getUuid(MediaAttachData $data): ?string
    {
        return $data->resourceExists ? '' : $data->fieldUuid;
    }

    private function rememberTargetModel(MediaAttachData $data, HasMedia $resourceModel): void
    {
        if (! $resourceModel instanceof TransientModel) {
            return;
        }

        /** @var Media $media */
        $media = config('media-library.media_model');

        $callback = function (Media $media) use ($data, $resourceModel): void {
            if ($media->collection_name !== $data->fieldUuid) {
                return;
            }

            TransientModel::setCustomPropertyValue(
                $media,
                $data->resource::$model,
                $data->field->collectionName,
            );

            $resourceModel->registerAllMediaConversions($media);
        };

        $media::creating($callback);
    }
}