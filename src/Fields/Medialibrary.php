<?php

namespace DmitryBubyakin\NovaMedialibraryField\Fields;

use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use InvalidArgumentException;
use Laravel\Nova\Fields\Field;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Http\Requests\NovaRequest;
use DmitryBubyakin\NovaMedialibraryField\Label;
use Spatie\MediaLibrary\Models\Media as MediaModel;
use DmitryBubyakin\NovaMedialibraryField\Resources\Media as MediaResource;

class Medialibrary extends Field
{
    use Concerns\SaveFilesFromRequest;

    /** @var string */
    public $component = 'nova-medialibrary-field';

    /** @var string */
    public $resourceName;

    /** @var string */
    public $resourceClass;

    /** @var string */
    public $relationName;

    /** @var string */
    public $collectionName;

    /** @var bool */
    public $multiple;

    /** @var bool */
    public $mediaSortable;

    /** @var array */
    public $imageMimes;

    /** @var callable|null */
    public $storeUsingCallback;

    /** @var callable|null */
    public $replaceUsingCallback;

    /** @var callable */
    public $thumbnailUrlCallback;

    /** @var callable */
    public $thumbnailTitleCallback;

    /** @var callable */
    public $thumbnailDescriptionCallback;

    /** @var \Illuminate\Support\Collection */
    public $labels;

    /** @var callable */
    public $mediaOnIndexCallback;

    /** @var string */
    public $textAlign = 'center';

    /** @var bool */
    public $showThumbnailDescription = false;

    /** @var string|null */
    public $accept;

    /** @var bool */
    public $croppable = false;

    public function __construct(string $name = 'Media', string $collection = 'default', string $resource = MediaResource::class)
    {
        parent::__construct($name);

        $this->resourceClass = $resource;
        $this->resourceName = $resource::uriKey();
        $this->labels = collect();

        $this
            ->relation('media')
            ->collection($collection)
            ->thumbnail('')
            ->thumbnailTitle('file_name')
            ->imageMimes('image/jpeg', 'image/gif', 'image/png')
            ->mediaOnIndex(1);
    }

    public function relation(string $relationName): self
    {
        $this->relationName = $relationName;

        return $this;
    }

    public function sortable($value = true)
    {
        $this->mediaSortable = $value;

        return $this;
    }

    public function collection(string $collectionName): self
    {
        $this->collectionName = $collectionName;

        $this->multiple = $this->isMultiple($collectionName);

        return $this;
    }

    public function imageMimes(string ...$imageMimes): self
    {
        $this->imageMimes = $imageMimes;

        return $this;
    }

    public function storeUsing(callable $storeUsingCallback): self
    {
        $this->storeUsingCallback = $storeUsingCallback;

        return $this;
    }

    public function replaceUsing(callable $replaceUsingCallback): self
    {
        $this->replaceUsingCallback = $replaceUsingCallback;

        return $this;
    }

    /**
     * @param string|callable $thumbnail
     *
     * @return $this
     */
    public function thumbnail($thumbnail): self
    {
        $this->validateStringOrCallable($thumbnail, __METHOD__);

        $this->thumbnailUrlCallback = $this->callback($thumbnail, function (MediaModel $media) use ($thumbnail) {
            return $media->getFullUrl($thumbnail);
        });

        return $this;
    }

    /**
     * @param string|callable $thumbnail
     *
     * @return $this
     */
    public function thumbnailTitle($title): self
    {
        $this->validateStringOrCallable($title, __METHOD__);

        $this->thumbnailTitleCallback = $this->callback($title, function (MediaModel $media) use ($title) {
            return data_get($media, str_replace('->', '.', $title));
        });

        return $this;
    }

    /**
     * @param string|callable $thumbnail
     * @param int $limit
     *
     * @return $this
     */
    public function thumbnailDescription($description, int $limit = 100): self
    {
        $this->validateStringOrCallable($description, __METHOD__);

        $this->showThumbnailDescription = true;

        $this->thumbnailDescriptionCallback = $this->callback($description, function (MediaModel $media) use ($description, $limit) {
            return str_limit(data_get($media, str_replace('->', '.', $description)), $limit);
        });

        return $this;
    }

    /**
     * @param  string $title
     * @param  string|callable $condition
     * @param  string|null $trueColor
     * @param  string|null $falseColor
     *
     * @return $this
     */
    public function label(string $title, $condition, $trueColor = 'var(--success)', $falseColor = 'var(--danger)'): self
    {
        $this->validateStringOrCallable($condition, __METHOD__);

        $resolver = $this->callback($condition, function (MediaModel $media) use ($condition) {
            return data_get($media, str_replace('->', '.', $condition));
        });

        $this->labels[] = new Label($title, $resolver, $trueColor, $falseColor);

        return $this;
    }

    /**
     * @param int|callable $mediaOnIndex
     *
     * @return $this
     */
    public function mediaOnIndex($mediaOnIndex = 1): self
    {
        $this->guardMediaOnIndex($mediaOnIndex);

        $this->mediaOnIndexCallback = $this->callback($mediaOnIndex, function (Collection $mediaItems) use ($mediaOnIndex) {
            return $mediaItems->take($mediaOnIndex);
        });

        $this->showOnIndex = true;

        return $this;
    }

    public function accept(string $accept): self
    {
        $this->accept = $accept;

        return $this;
    }

    public function croppable(): self
    {
        $this->croppable = true;

        return $this;
    }

    public function thumbnailSize(string $width, ?string $height = null): self
    {
        return $this->withMeta([
            'thumbnailWidth' => $width,
            'thumbnailHeight' => $height ?: $width,
        ]);
    }

    public function readonlyOnDetail(): self
    {
        return $this->withMeta(['readonlyOnDetail' => true]);
    }

    public function fileWrapperComponent(string $component): self
    {
        return $this->withMeta(['fileWrapperComponent' => $component]);
    }

    public function getCreationRules(NovaRequest $request): array
    {
        return [
            "{$this->attribute}.*.file" => parent::getCreationRules($request)[$this->attribute],
        ];
    }

    public function getUpdateRules(NovaRequest $request): array
    {
        return [
            "{$this->attribute}.*.file" => parent::getUpdateRules($request)[$this->attribute],
        ];
    }

    /**
     * @param mixed       $resource
     * @param string|null $attribute
     *
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $mediaItems = collect($resource->{$this->relationName});

        $this->value = $this->filterMedia($mediaItems)->map([$this, 'serializeMedia']);
    }

    public function serializeMedia(MediaModel $media): array
    {
        return [
            'id'                   => $media->id,
            'order'                => $media->order_column,
            'extension'            => $media->extension,
            'downloadUrl'          => $media->getFullUrl(),
            'customProperties'     => $media->custom_properties,
            'labels'               => $this->resolveLabels($media),
            'thumbnailUrl'         => $this->resolveThumbnailUrl($media),
            'thumbnailTitle'       => $this->resolveThumbnailTitle($media),
            'thumbnailDescription' => $this->resolveThumbnailDescription($media),
            'authorizedToView'     => $this->authorizedTo('view', $media),
            'authorizedToUpdate'   => $this->authorizedTo('update', $media),
            'authorizedToDelete'   => $this->authorizedTo('delete', $media),
        ];
    }

    protected function resolveLabels(MediaModel $media): array
    {
        return $this->labels->map->resolve($media)->all();
    }

    protected function resolveThumbnailUrl(MediaModel $media): ?string
    {
        return $this->isImage($media) ? call_user_func($this->thumbnailUrlCallback, $media) : null;
    }

    protected function resolveThumbnailTitle(MediaModel $media): ?string
    {
        return call_user_func($this->thumbnailTitleCallback, $media);
    }

    protected function resolveThumbnailDescription(MediaModel $media): ?string
    {
        return $this->showThumbnailDescription ? call_user_func($this->thumbnailDescriptionCallback, $media) : null;
    }

    protected function authorizedTo(string $ability, MediaModel $media): bool
    {
        return Gate::getPolicyFor($media) ? Gate::check($ability, $media) : true;
    }

    public function meta(): array
    {
        return array_merge([
            'accept'         => $this->accept,
            'croppable'      => $this->croppable,
            'mediaSortable'  => $this->mediaSortable,
            'collectionName' => $this->collectionName,
            'resourceName'   => $this->resourceName,
            'multiple'       => $this->multiple,
        ], $this->meta);
    }

    protected function filterMedia(Collection $mediaItems): Collection
    {
        if ($this->collectionName) {
            $mediaItems = $mediaItems->where('collection_name', $this->collectionName);
        }

        if ($this->showOnIndex && $this->isIndexView()) {
            $mediaItems = call_user_func($this->mediaOnIndexCallback, $mediaItems);
        }

        return $mediaItems->sortBy('order_column')->values();
    }

    protected function isIndexView(): bool
    {
        return collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))
                ->map->function
                ->contains('indexFields');
    }

    protected function isImage(MediaModel $media): bool
    {
        return in_array($media->mime_type, $this->imageMimes);
    }

    protected function isMultiple(string $collectionName): bool
    {
        $resource = $this->resolveResourceClass();

        $model = app($resource::$model);

        $model->registerMediaCollections();

        $collection = collect($model->mediaCollections)
            ->where('name', $collectionName)
            ->first();

        $singleFile = $collection->singleFile ?? false;

        return ! $singleFile;
    }

    protected function resolveResourceClass(): ?string
    {
        return collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8))
                ->map->class
                ->first(function (string $class) {
                    return is_subclass_of($class, Resource::class);
                });
    }

    protected function validateStringOrCallable($value, string $method)
    {
        if (! is_callable($value) && ! is_string($value)) {
            $this->invalidArgument($method, 'string or callable expected');
        }
    }

    protected function guardMediaOnIndex($mediaOnIndex): void
    {
        if (! is_callable($mediaOnIndex) && ! is_numeric($mediaOnIndex)) {
            $this->invalidArgument('mediaOnIndex', 'integer or callable expected');
        }

        if (is_numeric($mediaOnIndex) && (int) $mediaOnIndex <= 0) {
            $this->invalidArgument('mediaOnIndex', 'the number of media must be positive');
        }
    }

    protected function invalidArgument(string $method, string $message): void
    {
        throw new InvalidArgumentException("Medialibrary::{$method}: {$message}.");
    }

    protected function callback($value, callable $default): callable
    {
        return is_callable($value) ? $value : $default;
    }
}
