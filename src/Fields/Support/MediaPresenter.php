<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Support;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\Models\TransientModel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use function DmitryBubyakin\NovaMedialibraryField\call_or_default;

class MediaPresenter implements Arrayable
{
    private Media $media;

    private Medialibrary $field;

    public function __construct(Media $media, Medialibrary $field)
    {
        $this->media = $media;
        $this->field = $field;
    }

    public function downloadUrl(): ?string
    {
        $defaultCallback = function (): ?string {
            return $this->media->getFullUrl();
        };

        return call_or_default(
            $this->field->downloadCallback,
            [$this->media],
            $defaultCallback,
        );
    }

    public function previewUrl(): ?string
    {
        $mediaUrlCallback = function (): ?string {
            return Str::is('image/*', $this->media->mime_type)
                ? $this->media->getFullUrl()
                : null;
        };

        $mediaUrl = call_or_default(
            $this->field->previewCallback,
            [$this->media],
            $mediaUrlCallback,
        );

        $appendTimestampCallback = function (string $url): string {
            if ($this->field->appendTimestampToPreview) {
                return $url . '?timestamp=' . $this->media->updated_at->getTimestamp();
            }

            return $url;
        };

        return transform($mediaUrl, $appendTimestampCallback);
    }

    public function tooltip(): ?string
    {
        return call_or_default($this->field->tooltipCallback, [$this->media]);
    }

    public function title(): ?string
    {
        return call_or_default($this->field->titleCallback, [$this->media]);
    }

    public function copyAs(): array
    {
        return collect($this->field->copyAs)
            ->mapSpread(function (string $as, string $icon, callable $value): array {
                $value = (string) $value($this->media);

                return compact('as', 'icon', 'value');
            })->toArray();
    }

    public function attached(): bool
    {
        return $this->media->model_type !== TransientModel::class;
    }

    public function authorizedTo(string $ability): bool
    {
        return !Gate::getPolicyFor($this->media) or Gate::check($ability, $this->media);
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
            'copyAs' => $this->copyAs(),
            'attached' => $this->attached(),
            'authorizedToView' => $this->authorizedTo('view'),
            'authorizedToUpdate' => $this->authorizedTo('update'),
            'authorizedToDelete' => $this->authorizedTo('delete'),
        ], $this->cropperOptions());
    }

    public function cropperOptions(): array
    {
        $options = call_or_default($this->field->cropperOptionsCallback, [$this->media]);

        $enabled = ! is_null($options);

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
        $manipulations = $this->getMediaManipulations();
        $manualCrop = $this->resolveMediaCropperData();

        return array_map('intval', array_filter([
            'rotate' => $manipulations['orientation'] ?? null,
            'width' => $manualCrop[0] ?? null,
            'height' => $manualCrop[1] ?? null,
            'x' => $manualCrop[2] ?? null,
            'y' => $manualCrop[3] ?? null,
        ], 'is_numeric'));
    }

    private function getMediaManipulations(): array
    {
        return $this->media->manipulations[$this->field->cropperConversion] ?? [];
    }

    private function resolveMediaCropperData(): array
    {
        $manipulations = $this->getMediaManipulations();

        if (isset($manipulations['manualCrop']) and ! empty($manipulations['manualCrop'])) {
            if (is_string($manipulations['manualCrop'])) {
                $manualCrop = explode(',', $manipulations['manualCrop']);
            } else {
                $manualCrop = $manipulations['manualCrop'];
            }
        } else {
            $manualCrop = [];
        }

        return $manualCrop;
    }
}
