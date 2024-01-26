<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Support;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AttachableMediaPresenter implements Arrayable
{
    private Media $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function toArray(): array
    {
        return array_merge([
            'id' => $this->media->id,
            'size' => $this->media->size,
            'mimeType' => $this->media->mime_type,
            'fileName' => $this->media->file_name,
            'collectionName' => $this->media->collection_name,
            'extension' => $this->media->extension,
            'previewUrl' => $this->media->getFullUrl(),
        ]);
    }
}
