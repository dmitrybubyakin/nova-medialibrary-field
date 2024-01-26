<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields;

use DmitryBubyakin\NovaMedialibraryField\Fields\Support\AttachCallback;
use DmitryBubyakin\NovaMedialibraryField\Fields\Support\MediaCollectionRules;
use DmitryBubyakin\NovaMedialibraryField\Fields\Support\MediaFields;
use DmitryBubyakin\NovaMedialibraryField\Fields\Support\MediaPresenter;
use DmitryBubyakin\NovaMedialibraryField\Fields\Support\ResolveMediaCallback;
use DmitryBubyakin\NovaMedialibraryField\Models\TransientModel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Whitecube\NovaFlexibleContent\Flexible;
use Whitecube\NovaFlexibleContent\Layouts\Layout;
use function DmitryBubyakin\NovaMedialibraryField\callable_or_default;

/**
 * @method static static make(string $name, string $collectionName = '', string $diskName = '', string $attribute = null)
 */
class Medialibrary extends Field
{
    public string $collectionName;

    public string $diskName;

    public mixed $fieldsCallback;

    public mixed $attachCallback;

    public mixed $resolveMediaUsingCallback;

    public bool $single;

    public mixed $mediaOnIndexCallback;

    public mixed $attachRules;

    public $component = 'nova-medialibrary-field';

    public array $copyAs = [];

    public mixed $attachExistingCallback = null;

    public mixed $downloadCallback = null;

    public mixed $previewCallback = null;

    public bool $appendTimestampToPreview = false;

    public mixed $tooltipCallback = null;

    public mixed $titleCallback = null;

    public ?string $cropperConversion = null;

    public mixed $cropperOptionsCallback = null;

    public mixed $moveMediaToTargetModelCallback = null;

    public function __construct(
        string $name,
        string $collectionName = '',
        string $diskName = '',
        string $attribute = null,
    )
    {
        parent::__construct($name, $attribute);

        $this->collectionName = $collectionName;
        $this->diskName = $diskName;

        $this->fields(MediaFields::make());
        $this->attachUsing(new AttachCallback);
        $this->resolveMediaUsing(new ResolveMediaCallback);
        $this->single(false);
        $this->mediaOnIndex(1);
        $this->attachRules([]);
        $this->resolve(null);
    }

    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function fields(callable $callback): self
    {
        $this->fieldsCallback = $callback;

        return $this;
    }

    public function attachUsing(callable $callback): self
    {
        $this->attachCallback = $callback;

        return $this;
    }

    public function attachExisting(string | callable | null $callback = null): self
    {
        $defaultCallback = function (Builder $query) use ($callback): void {
            if ($callback) {
                $query->where('collection_name', $callback);
            }
        };

        $this->attachExistingCallback = callable_or_default($callback, $defaultCallback);

        return $this->withMeta(['attachExisting' => true]);
    }

    public function resolveMediaUsing(callable $mediaCallback): self
    {
        $this->resolveMediaUsingCallback = $mediaCallback;

        return $this;
    }

    public function mediaOnIndex(int | callable $mediaOnIndex): self
    {
        $defaultCallback = function (HasMedia $resource, string $collectionName) use ($mediaOnIndex): Collection {
            return $resource
                ->media()
                ->where('collection_name', $collectionName)
                ->limit($mediaOnIndex)
                ->orderBy('order_column')
                ->get();
        };

        $this->mediaOnIndexCallback = callable_or_default($mediaOnIndex, $defaultCallback);

        return $this;
    }

    public function downloadUsing(string | callable $downloadUsing): self
    {
        $defaultCallback = function (Media $media) use ($downloadUsing): ?string {
            return $media->getFullUrl($downloadUsing);
        };

        $this->downloadCallback = callable_or_default($downloadUsing, $defaultCallback);

        return $this;
    }

    public function previewUsing(string | callable $previewUsing): self
    {
        $defaultCallback = function (Media $media) use ($previewUsing): ?string {
            return $media->getFullUrl($previewUsing);
        };

        $this->previewCallback = callable_or_default($previewUsing, $defaultCallback);

        return $this;
    }

    public function appendTimestampToPreview(bool $append = true): self
    {
        $this->appendTimestampToPreview = $append;

        return $this;
    }

    public function tooltip(string | callable $tooltip): self
    {
        $defaultCallback = function (Media $media) use ($tooltip): ?string {
            return $media->{$tooltip};
        };

        $this->tooltipCallback = callable_or_default($tooltip, $defaultCallback);

        return $this;
    }

    public function title(string | callable $title): self
    {
        $defaultCallback = function (Media $media) use ($title): ?string {
            return $media->{$title};
        };

        $this->titleCallback = callable_or_default($title, $defaultCallback);

        return $this;
    }

    public function copyAs(string $as, string | callable $value, string $icon = 'link'): self
    {
        $defaultCallback = function (Media $media) use ($value): ?string {
            return $media->{$value};
        };

        $this->copyAs[] = [
            $as,
            $icon,
            callable_or_default($value, $defaultCallback),
        ];

        return $this;
    }

    public function hideCopyUrlAction(): self
    {
        return $this->withMeta(['hideCopyUrlAction' => true]);
    }

    public function croppable(string $conversion, array | callable |null $options = null): self
    {
        $this->cropperConversion = $conversion;

        $defaultCallback = function () use ($options): array {
            return $options ?: [
                'viewMode' => 3,
            ];
        };

        $this->cropperOptionsCallback = callable_or_default($options, $defaultCallback);

        $this->appendTimestampToPreview();

        return $this;
    }

    public function moveMediaToTargetModelUsing(callable $moveMediaToTargetModelCallback): self
    {
        $this->moveMediaToTargetModelCallback = $moveMediaToTargetModelCallback;

        return $this;
    }

    public function single(bool $single = true): self
    {
        $this->single = $single;

        return $this;
    }

    public function accept(string $accept): self
    {
        return $this->withMeta(['accept' => $accept]);
    }

    public function maxSizeInBytes(int $maxSize): self
    {
        return $this->withMeta(['maxSize' => $maxSize]);
    }

    public function attachOnDetails(): self
    {
        return $this->withMeta(['attachOnDetails' => true]);
    }

    public function autouploading(): self
    {
        return $this->withMeta(['autouploading' => true]);
    }

    public function attachRules(ValidationRule | string | array $rules): self
    {
        $this->attachRules = ($rules instanceof ValidationRule || is_string($rules)) ? func_get_args() : $rules;

        return $this;
    }

    public function getAttachRules(NovaRequest $request): array
    {
        return is_callable($this->attachRules)
            ? call_user_func($this->attachRules, $request)
            : $this->attachRules;
    }

    public function getCreationRules(NovaRequest $request): array
    {
        return $this->makeMediaCollectionRules($request, parent::getCreationRules($request));
    }

    public function getUpdateRules(NovaRequest $request): array
    {
        return $this->makeMediaCollectionRules($request, parent::getUpdateRules($request));
    }

    protected function makeMediaCollectionRules(NovaRequest $request, array $rules): array
    {
        return [
            $this->attribute => MediaCollectionRules::make($rules[$this->attribute], $request, $this),
        ];
    }

    public function resolve($resource, $attribute = null): void
    {
        $this->value = (string) Str::uuid();
    }

    public function resolveForDisplay($resource, $attribute = null): void
    {
        $controllerActions = [
            'Laravel\Nova\Http\Controllers\ResourceIndexController', // Index controller action
            'Laravel\Nova\Http\Controllers\LensController@show', // Lens controller action
        ];

        $actionName = Route::current()->getActionName();

        if (in_array($actionName, $controllerActions)) {
            $this->resolveForIndex($resource);
        }
    }

    public function resolveForIndex($resource): void
    {
        $callback = function (Media $media): MediaPresenter {
            return new MediaPresenter($media, $this);
        };

        $this->value = call_user_func_array(
            $this->mediaOnIndexCallback,
            [$resource, $this->collectionName],
        )
            ->map($callback);
    }

    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute): callable
    {
        $callback = null;

        if (is_callable($this->fillCallback)) {
            $callback = call_user_func(
                $this->fillCallback,
                $request,
                $model,
                $attribute,
                $requestAttribute
            );
        }

        if (is_a($model, Layout::class)) {
            $model = Flexible::getOriginModel();
        }

        return function () use ($request, $attribute, $model, $callback): void {
            if (is_callable($callback)) {
                $callback();
            }

            if (empty($uuid = $request->input($attribute))) {
                return;
            }

            foreach (TransientModel::make()->getMedia($uuid) as $media) {
                $this->moveMediaToTargetModel($media, $model);
            }
        };
    }

    public function moveMediaToTargetModel(Media $media, HasMedia $target): void
    {
        $propertyName = TransientModel::getCustomPropertyName();

        $callback = function (Media $media, HasMedia $target, string $propertyName): void {
            $media
                ->forgetCustomProperty($propertyName)
                ->move($target, $this->collectionName, $this->diskName)
                ->update([
                    'manipulations' => $media->manipulations,
                ]);
        };

        $moveMediaToTargetModelCallback = callable_or_default(
            $this->moveMediaToTargetModelCallback,
            $callback,
        );

        $moveMediaToTargetModelCallback($media, $target, $propertyName);
    }

    public function meta(): array
    {
        return array_merge([
            'collectionName' => $this->collectionName,
            'single' => $this->single,
        ], $this->meta);
    }
}
