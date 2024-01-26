<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\Field;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GeneratedConversions extends Field
{
    public $component = 'nova-generated-conversions-field';

    public $showOnIndex = false;

    public function __construct(string $name)
    {
        $callback = function (Media $media): Collection {
            $mapCallback = function (string $conversionName) use ($media): array {
                return [
                    $conversionName => $media->getFullUrl($conversionName),
                ];
            };

            return $media
                ->getGeneratedConversions()
                ->filter()
                ->keys()
                ->mapWithKeys($mapCallback);
        };

        parent::__construct($name, $callback);
    }

    public function withTooltips(bool $withTooltips = true): self
    {
        return $this->withMeta(['withTooltips' => $withTooltips]);
    }
}
