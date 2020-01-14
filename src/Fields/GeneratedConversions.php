<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields;

use Laravel\Nova\Fields\Field;
use Spatie\MediaLibrary\Models\Media;

class GeneratedConversions extends Field
{
    public $component = 'nova-generated-conversions-field';

    public $showOnIndex = false;

    public function __construct($name)
    {
        parent::__construct($name, function (Media $media) {
            return $media
                ->getGeneratedConversions()
                ->filter()
                ->keys()
                ->mapWithKeys(function (string $conversionName) use ($media) {
                    return [$conversionName => $media->getFullUrl($conversionName)];
                });
        });
    }

    public function withTooltips(bool $withTooltips = true): self
    {
        return $this->withMeta(['withTooltips' => $withTooltips]);
    }
}
