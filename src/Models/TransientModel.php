<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @method static self make()
 */
class TransientModel extends Model implements HasMedia
{
    use InteractsWithMedia;

    public static string $customPropertyName = '__target_props__';

    public $timestamps = false;

    protected $guarded = [];

    public function newInstance($attributes = [], $exists = false): self
    {
        $attributes = array_merge(
            TransientModel::instanceAttributes(),
            $attributes,
        );

        return parent::newInstance($attributes, true);
    }

    public static function instanceAttributes(): array
    {
        return ['id' => '1'];
    }

    public function newBaseQueryBuilder(): Builder
    {
        return new class($this->getConnection()) extends Builder {
            public function get($columns = ['*']): Collection
            {
                return collect([(object) TransientModel::instanceAttributes()]);
            }
        };
    }

    public function staleMedia(): MorphMany
    {
        return $this
            ->media()
            ->where('created_at', '<=', now()->subDay());
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

        foreach ($this->mediaCollections as $collection) {
            if ($collection->name === $collectionName) {
                $collection->name = $media->collection_name;
            }
        }

        foreach ($this->mediaConversions as $conversion) {
            if (in_array($collectionName, $conversion->getPerformOnCollections())) {
                $conversion->performOnCollections($media->collection_name);
            }

            $conversion->nonQueued();
        }
    }

    public static function getCustomPropertyValue(?Media $media): array
    {
        $value = $media?->getCustomProperty(static::getCustomPropertyName());

        if (is_null($media) || is_null($value)) {
            return [null, null];
        }

        return [
            $value['target'],
            $value['collectionName'],
        ];
    }
}
