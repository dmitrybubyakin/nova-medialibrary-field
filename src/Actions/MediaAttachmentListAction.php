<?php

namespace DmitryBubyakin\NovaMedialibraryField\Actions;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaAttachmentListData;
use DmitryBubyakin\NovaMedialibraryField\Fields\Support\AttachableMediaPresenter;
use DmitryBubyakin\NovaMedialibraryField\Models\TransientModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use function DmitryBubyakin\NovaMedialibraryField\call_or_default;

class MediaAttachmentListAction
{
    public function handle(MediaAttachmentListData $data): LengthAwarePaginator
    {
        $resourceModel = $this->getResourceModel($data);

        $queryBuilder = $this->getBaseQueryBuilder();

        $queryBuilder = $this->applyNameFilter($queryBuilder, $data->name);
        $queryBuilder = $this->applyMaxSizeFilter($queryBuilder, $data->maxSize);
        $queryBuilder = $this->applyMimeTypeFilter($queryBuilder, $data->mimeType);

        $queryBuilder = call_or_default(
            $data->field->attachExistingCallback,
            [$queryBuilder, $data->request, $resourceModel],
        ) ?: $queryBuilder;

        return $queryBuilder
            ->paginate($data->perPage)
            ->through(fn (Media $media): AttachableMediaPresenter => new AttachableMediaPresenter($media));
    }

    private function getResourceModel(MediaAttachmentListData $data): HasMedia
    {
        return $data->resourceExists
            ? $data->resourceModel
            : TransientModel::make();
    }

    private function getBaseQueryBuilder(): Builder
    {
        /** @var Media $media */
        $media = config('media-library.media_model');

        return $media::query();
    }

    private function applyNameFilter(Builder $builder, ?string $name): Builder
    {
        $callback = function (Builder $builder) use ($name): Builder {
            return $builder->where(function (Builder $builder) use ($name): Builder {
                return $builder
                    ->where('name', 'like', '%' . $name . '%')
                    ->orWhere('file_name', 'like', '%' . $name . '%');
            });
        };

        return $builder->when(!empty($name), $callback);
    }

    private function applyMaxSizeFilter(Builder $builder, ?int $maxSize): Builder
    {
        $callback = function (Builder $builder) use ($maxSize): Builder {
            return $builder->where('size', '<=', $maxSize);
        };

        return $builder->when(!empty($maxSize), $callback);
    }

    private function applyMimeTypeFilter(Builder $builder, ?string $mimeType): Builder
    {
        $callback = function (Builder $builder) use ($mimeType): Builder {
            if (str_contains($mimeType, ',')) {
                $mimeTypes = explode(',', $mimeType);

                return $builder->whereIn('mime_type', $mimeTypes);
            }

            $mimeType = str_replace('*', '%', $mimeType);

            return $builder->where('mime_type', 'like', $mimeType);
        };

        return $builder->when(!empty($mimeType), $callback);
    }
}