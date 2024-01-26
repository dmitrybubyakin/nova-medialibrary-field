<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Support;

use DmitryBubyakin\NovaMedialibraryField\Models\TransientModel;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AttachCallback
{
    public function __invoke(
        HasMedia $model,
        UploadedFile $file,
        string $collectionName,
        string $diskName,
        ?string $fieldUuid
    ): Media {
        // you can override this behaviour: Medialibrary::attachUsing()
        if ($model instanceof TransientModel) {
            $collectionName = $fieldUuid;
        }

        return $model
            ->addMedia($file)
            ->toMediaCollection($collectionName, $diskName);
    }
}
