<?php

namespace DmitryBubyakin\NovaMedialibraryField\Fields;

use Exception;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use InvalidArgumentException;
use Laravel\Nova\Fields\Field;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\Models\Media as MediaModel;
use DmitryBubyakin\NovaMedialibraryField\Resources\Media as MediaResource;

class Medialibrary extends Field
{
    /**
     * The element's component.
     *
     * @var string
     */
    public $component = 'nova-medialibrary-field';

    /**
     * The URI key of the media resource.
     *
     * @var string
     */
    public $resourceName;

    /**
     * The class name of the media resource.
     *
     * @var string
     */
    public $resourceClass;

    /**
     * The name of the relationship.
     *
     * @var string
     */
    public $relationName;

    /**
     * The name of the collection.
     *
     * @var string
     */
    public $collectionName;

    /**
     * Indicates if the collection is not a single file collection.
     *
     * @var bool
     */
    public $multiple;

    /**
     * Indicates if the media items are sortable.
     *
     * @var bool
     */
    public $mediaSortable;

    /**
     * Image mime types.
     *
     * @var array
     */
    public $imageMimes;

    /**
     * @var callable|null
     */
    public $storeUsingCallback;

    /**
     * @var callable|null
     */
    public $replaceUsingCallback;

    /**
     * The callback used to retrieve the thumbnail URL.
     *
     * @var callable
     */
    public $thumbnailUrlCallback;

    /**
     * The callback used to retrieve the thumbnail title.
     *
     * @var callable
     */
    public $thumbnailTitleCallback;

    /**
     * The callback used to retrieve the thumbnail description.
     *
     * @var callable
     */
    public $thumbnailDescriptionCallback;

    /**
     * Resolve the media which should be shown on the index view.
     *
     * @var callable
     */
    public $mediaOnIndexCallback;

    /**
     * The text alignment for the field's text in tables.
     *
     * @var string
     */
    public $textAlign = 'center';

    /**
     * Indicates if thumbnails should be big.
     *
     * @var bool
     */
    public $bigThumbnails = false;

    /**
     * Indicates if the thumbnail description should be shown.
     *
     * @var bool
     */
    public $showThumbnailDescription = false;

    /**
     * Available mime types for file input.
     *
     * @var string|null
     */
    public $accept;

    public function __construct(string $name = 'Media', string $collection = 'default', string $resource = MediaResource::class)
    {
        parent::__construct($name);

        $this->resourceClass = $resource;
        $this->resourceName = $resource::uriKey();

        $this
            ->relation('media')
            ->collection($collection)
            ->thumbnail('')
            ->thumbnailTitle('file_name')
            ->imageMimes('image/jpeg', 'image/gif', 'image/png')
            ->mediaOnIndex(1)
            ->exceptOnForms();
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
        $this->guardThumbnail($thumbnail);

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
        $this->guardThumbnailTitle($title);

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
        $this->guardThumbnailDescription($description);

        $this->showThumbnailDescription = true;

        $this->thumbnailDescriptionCallback = $this->callback($description, function (MediaModel $media) use ($description, $limit) {
            return str_limit(data_get($media, str_replace('->', '.', $description)), $limit);
        });

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

    public function bigThumbnails(): self
    {
        $this->bigThumbnails = true;

        return $this;
    }

    public function accept(string $accept): self
    {
        $this->accept = $accept;

        return $this;
    }

    public function onlyOnForms()
    {
        throw new Exception('Medialibrary::onlyOnForms: can\'t be shown on forms.');
    }

    /**
     * Resolve the field's value.
     *
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
            'thumbnailUrl'         => $this->resolveThumbnailUrl($media),
            'thumbnailTitle'       => $this->resolveThumbnailTitle($media),
            'thumbnailDescription' => $this->resolveThumbnailDescription($media),
            'authorizedToView'     => $this->authorizedTo('view', $media),
            'authorizedToUpdate'   => $this->authorizedTo('update', $media),
            'authorizedToDelete'   => $this->authorizedTo('delete', $media),
        ];
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
            'bigThumbnails'  => $this->bigThumbnails,
            'mediaSortable'  => $this->mediaSortable,
            'collectionName' => $this->collectionName,
            'resourceName'   => $this->resourceName,
            'relationName'   => $this->relationName,
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

    protected function guardThumbnail($thumbnail): void
    {
        if (! is_callable($thumbnail) && ! is_string($thumbnail)) {
            $this->stringOrCallableExpected('thumbnail');
        }
    }

    protected function guardThumbnailTitle($title): void
    {
        if (! is_callable($title) && ! is_string($title)) {
            $this->stringOrCallableExpected('thumbnailTitle');
        }
    }

    protected function guardThumbnailDescription($description): void
    {
        if (! is_callable($description) && ! is_string($description)) {
            $this->stringOrCallableExpected('thumbnailDescription');
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

    protected function stringOrCallableExpected(string $method): void
    {
        $this->invalidArgument($method, 'string or callable expected');
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
