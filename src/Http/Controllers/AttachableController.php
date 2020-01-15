<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use function DmitryBubyakin\NovaMedialibraryField\call_or_default;
use DmitryBubyakin\NovaMedialibraryField\Fields\Support\AttachableMediaPresenter;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MedialibraryRequest;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\Models\Media;

class AttachableController
{
    public function __invoke(MedialibraryRequest $request): JsonResponse
    {
        $field = $request->medialibraryField();

        $model = $request->resourceExists() ? $request->findModelOrFail() : TransientModel::make();

        $query = $this->buildQuery($request, $model);

        $query = call_or_default($field->attachExistingCallback, [$query, $request, $model]) ?: $query;

        $paginator = $query->simplePaginate($request->input('perPage', 25));

        return response()->json(
            $paginator->setCollection(
                $paginator->getCollection()->mapInto(AttachableMediaPresenter::class)
            )
        );
    }

    private function buildQuery(MedialibraryRequest $request): Builder
    {
        return Media::query()->when($request->input('name'), function (Builder $query, string $name): void {
            $query->where(function (Builder $query) use ($name): void {
                $query
                    ->where('name', 'like', "%{$name}%")
                    ->orWhere('file_name', 'like', "%{$name}%");
            });
        })->when($request->input('maxSize'), function (Builder $query, int $maxSize): void {
            $query->where('size', '<=', $maxSize);
        })->when($request->input('mimeType'), function (Builder $query, string $mimeType): void {
            $query->where('mime_type', 'like', str_replace('*', '%', $mimeType));
        });
    }
}
