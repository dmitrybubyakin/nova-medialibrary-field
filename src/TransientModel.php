<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class TransientModel extends Model implements HasMedia
{
    use HasMediaTrait;

    public static $customPropertyName = '__target_props__';

    public $timestamps = false;

    protected $guarded = [];

    public function newInstance($attributes = [], $exists = false)
    {
        return parent::newInstance(TransientModel::instanceAttributes() + $attributes, true);
    }

    public function newBaseQueryBuilder()
    {
        return new class($this->getConnection()) extends Builder {
            public function get($columns = ['*'])
            {
                return collect([(object) TransientModel::instanceAttributes()]);
            }
        };
    }

    public function staleMedia(): MorphMany
    {
        return $this->media()->where('created_at', '<=', now()->subDays(1));
    }

    public static function instanceAttributes(): array
    {
        return ['id' => '1'];
    }

    public static function setCustomPropertyName(string $name): void
    {
        static::$customPropertyName = $name;
    }

    public static function getCustomPropertyName(): string
    {
        return static::$customPropertyName;
    }

    public static function setCustomPropertyValue(Media $media, string $target, string $collectionName): void
    {
        $media->setCustomProperty(static::getCustomPropertyName(), [
            'target' => $target,
            'collectionName' => $collectionName,
        ]);
    }

    public static function getCustomPropertyValue(?Media $media): array
    {
        if (
            is_null($media) ||
            is_null($value = $media->getCustomProperty(static::getCustomPropertyName()))
        ) {
            return [null, null];
        }

        return [$value['target'], $value['collectionName']];
    }

    public function registerAllMediaConversions(Media $media = null): void
    {
        [$modelClassName, $collectionName] = $this->getCustomPropertyValue($media);

        if (is_null($modelClassName)) {
            return;
        }

        $model = new $modelClassName;

        $model->registerAllMediaConversions($media);

        $this->mediaConversions = $model->mediaConversions;
        $this->mediaCollections = $model->mediaCollections;

        foreach ($this->mediaConversions as $conversion) {
            if (in_array($collectionName, $conversion->getPerformOnCollections())) {
                $conversion->performOnCollections($media->collection_name);
            }

            $conversion->nonQueued();
        }
    }
}
