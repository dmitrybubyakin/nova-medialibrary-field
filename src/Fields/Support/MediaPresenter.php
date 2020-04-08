<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Support;

use function DmitryBubyakin\NovaMedialibraryField\call_or_default;
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPresenter implements Arrayable
{
    private $media;
    private $field;

    public function __construct(Media $media, Medialibrary $field)
    {
        $this->media = $media;
        $this->field = $field;
    }

    public function downloadUrl(): ?string
    {
        return call_or_default($this->field->downloadCallback, [$this->media], function () {
            return $this->media->getFullUrl();
        });
    }

    public function previewUrl(): ?string
    {
        return call_or_default($this->field->previewCallback, [$this->media], function () {
            return Str::is('image/*', $this->media->mime_type)
                ? $this->media->getFullUrl()
                : null;
        });
    }

    public function tooltip(): ?string
    {
        return call_or_default($this->field->tooltipCallback, [$this->media]);
    }

    public function title(): ?string
    {
        return call_or_default($this->field->titleCallback, [$this->media]);
    }

    public function attached(): bool
    {
        return $this->media->model_type !== TransientModel::class;
    }

    public function authorizedTo(string $ability): bool
    {
        return Gate::getPolicyFor($this->media) ? Gate::check($ability, $this->media) : true;
    }

    public function toArray(): array
    {
        return array_merge([
            'id' => $this->media->id,
            'order' => $this->media->order_column,
            'fileName' => $this->media->file_name,
            'extension' => $this->media->extension,
            'downloadUrl' => $this->downloadUrl(),
            'previewUrl' => $this->previewUrl(),
            'tooltip' => $this->tooltip(),
            'title' => $this->title(),
            'attached' => $this->attached(),
            'authorizedToView' => $this->authorizedTo('view'),
            'authorizedToUpdate' => $this->authorizedTo('update'),
            'authorizedToDelete' => $this->authorizedTo('delete'),
        ], $this->cropperOptions());
    }

    public function cropperOptions(): array
    {
        $options = call_or_default($this->field->cropperOptionsCallback, [$this->media]);

        $enabled = $options !== null;

        return [
            'cropperEnabled' => $enabled,
            'cropperOptions' => $options,
            'cropperMediaUrl' => $this->media->getFullUrl(),
            'cropperConversion' => $this->field->cropperConversion,
            'cropperData' => $enabled ? ($this->cropperData() ?: null) : null,
        ];
    }

    public function cropperData(): array
    {
        $manipulations = $this->media->manipulations[$this->field->cropperConversion] ?? [];

        $manualCrop = explode(',', $manipulations['manualCrop'] ?? '');

        return array_map('intval', array_filter([
            'rotate' => $manipulations['orientation'] ?? null,
            'width' => $manualCrop[0] ?? null,
            'height' => $manualCrop[1] ?? null,
            'x' => $manualCrop[2] ?? null,
            'y' => $manualCrop[3] ?? null,
        ], 'is_numeric'));
    }
}
