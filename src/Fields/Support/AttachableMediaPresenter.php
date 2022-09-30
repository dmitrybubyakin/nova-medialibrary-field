<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Support;

use function DmitryBubyakin\NovaMedialibraryField\call_or_default;
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AttachableMediaPresenter implements Arrayable
{
    private $media;
    private $field;

    public function __construct(Media $media, Medialibrary $field)
    {
        $this->media = $media;
        $this->field = $field;
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
            'previewUrl' => $this->previewUrl(),
        ]);
    }

    public function previewUrl(): ?string
    {
        return transform(call_or_default($this->field->previewCallback, [$this->media], function () {
            return Str::is('image/*', $this->media->mime_type)
                ? $this->media->getFullUrl()
                : null;
        }), function (string $url): string {
            if ($this->field->appendTimestampToPreview) {
                return $url . '?timestamp=' . $this->media->updated_at->getTimestamp();
            }

            return $url;
        });
    }
}
