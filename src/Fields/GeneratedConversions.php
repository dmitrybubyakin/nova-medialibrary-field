<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields;

use Laravel\Nova\Fields\Field;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use function DmitryBubyakin\NovaMedialibraryField\validate_args;
use function DmitryBubyakin\NovaMedialibraryField\callable_or_default;

class GeneratedConversions extends Field
{
    public $component = 'nova-generated-conversions-field';

    private $previewCallback;
    private $copyAsCallback;

    public $showOnIndex = false;

    public function __construct($name)
    {
        $this->previewUsing();
        $this->copyAs();

        parent::__construct($name, function (Media $media) {
            return $media
                ->getGeneratedConversions()
                ->filter()
                ->keys()
                ->mapWithKeys(function (string $conversionName) use ($media) {
                    return [$conversionName => [
                        'previewUrl' => call_user_func($this->previewCallback, $media, $conversionName),
                        'copyAsUrl' => call_user_func($this->copyAsCallback, $media, $conversionName)
                    ]];
                });
        });
    }

    public function withTooltips(bool $withTooltips = true): self
    {
        return $this->withMeta(['withTooltips' => $withTooltips]);
    }

    /**
     * @param string|callable|null $previewUsing
     */
    public function previewUsing($previewUsing = null): self
    {
        validate_args();

        $this->previewCallback = callable_or_default(
            $previewUsing,
            function (Media $media, string $conversionName) use ($previewUsing): ?string {
                return $media->getFullUrl($previewUsing ?: $conversionName);
            }
        );

        return $this;
    }

    /**
     * @param string|callable|null copyAs$
     */
    public function copyAs($copyAs = null): self
    {
        validate_args();

        $this->copyAsCallback = callable_or_default(
            $copyAs,
            function (Media $media, string $conversionName) use ($copyAs): ?string {
                return $media->getFullUrl($copyAs ?: $conversionName);
            }
        );

        return $this;
    }
}
