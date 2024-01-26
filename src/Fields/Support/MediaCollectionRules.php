<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Support;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\Models\TransientModel;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ClosureValidationRule;
use Laravel\Nova\Http\Requests\NovaRequest;

class MediaCollectionRules
{
    public static function make(array $rules, NovaRequest $request, Medialibrary $field): array
    {
        if (empty($rules)) {
            return [];
        }

        $callback = function ($attribute, $uuid, $fail) use ($rules, $request, $field): void {
            $media = static::getMedia(
                $request,
                $uuid,
                $field->collectionName,
                $field->resolveMediaUsingCallback,
            );

            $temporaryAttribute = ':value';

            $validator = Validator::make(
                [$temporaryAttribute => $media],
                [$temporaryAttribute => $rules],
            );

            if ($validator->fails()) {
                $fail(Str::replaceFirst(
                    $temporaryAttribute,
                    $attribute,
                    $validator->errors()->first($temporaryAttribute),
                ));
            }
        };

        return [
            new class($callback) extends ClosureValidationRule implements ImplicitRule {
                //
            },
        ];
    }

    private static function getMedia(NovaRequest $request, string $uuid, string $collectionName, callable $resolver): array
    {
        [$model, $collectionName] = is_null($request->route('resourceId'))
            ? [TransientModel::make(), $uuid]
            : [$request->findModelOrFail(), $collectionName];

        return collect(call_user_func($resolver, $model, $collectionName))->toArray();
    }
}
