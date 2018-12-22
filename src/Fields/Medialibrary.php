<?php

namespace DmitryBubyakin\NovaMedialibraryField\Fields;

use Exception;
use Laravel\Nova\Nova;
use InvalidArgumentException;
use Laravel\Nova\Fields\Field;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Http\Requests\NovaRequest;
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
            'id'                 => $media->id,
            'order'              => $media->order_column,
            'filename'           => $media->file_name,
            'extension'          => $media->extension,
            'downloadUrl'        => $media->getFullUrl(),
            'thumbnailUrl'       => $this->isImage($media) ? call_user_func($this->thumbnailUrlCallback, $media) : null,
            'authorizedToView'   => Gate::check('view', $media),
            'authorizedToUpdate' => Gate::check('update', $media),
            'authorizedToDelete' => Gate::check('delete', $media),
        ];
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
        $request = app(NovaRequest::class);

        $resource = Nova::resourceForKey($request->viaResource ?? $request->resource);

        $model = app($resource::$model);

        $model->registerMediaCollections();

        $collection = collect($model->mediaCollections)
            ->where('name', $collectionName)
            ->first();

        $singleFile = $collection->singleFile ?? false;

        return ! $singleFile;
    }

    protected function guardThumbnail($thumbnail): void
    {
        if (! is_callable($thumbnail) && ! is_string($thumbnail)) {
            throw new InvalidArgumentException('Medialibrary::thumbnail: string or callable expected.');
        }
    }

    protected function guardMediaOnIndex($mediaOnIndex): void
    {
        if (! is_callable($mediaOnIndex) && ! is_numeric($mediaOnIndex)) {
            throw new InvalidArgumentException('Medialibrary::mediaOnIndex: integer or callable expected.');
        }

        if (is_numeric($mediaOnIndex) && (int) $mediaOnIndex <= 0) {
            throw new InvalidArgumentException('Medialibrary::mediaOnIndex: the number of media must be positive.');
        }
    }

    protected function callback($value, callable $default): callable
    {
        return is_callable($value) ? $value : $default;
    }
}
