<?php

namespace DmitryBubyakin\NovaMedialibraryField\Fields\Concerns;

use Spatie\Image\Image;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Laravel\Nova\Http\Requests\NovaRequest;

trait SaveFilesFromRequest
{
    /**
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return mixed
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute): void
    {
        $files = array_filter($request->{$this->collectionName} ?? []);

        if (! $files) {
            return;
        }

        $model::saved(function (HasMedia $model) use ($files) {
            foreach ($files as $file) {
                $this->attachFile($model, $file['file'], $file['cropperData'] ?? []);
            }
        });
    }

    public function attachFile(HasMedia $model, UploadedFile $file, array $cropperData = null): array
    {
        if ($this->croppable && $cropperData) {
            $this->cropImage($file, $cropperData);
        }

        $fileAdder = $model->addMedia($file);

        $oldFile = $this->multiple ? null : $model->getFirstMedia($this->collectionName);

        if ($oldFile && is_callable($this->replaceUsingCallback)) {
            $fileAdder = call_user_func($this->replaceUsingCallback, $fileAdder, $oldFile, $file);
        } elseif (is_callable($this->storeUsingCallback)) {
            $fileAdder = call_user_func($this->storeUsingCallback, $fileAdder, $file);
        }

        return $this->serializeMedia($fileAdder->toMediaCollection($this->collectionName));
    }

    public function cropImage(string $pathToImage, array $data): void
    {
        Image::load($pathToImage)->manualCrop(
            $data['width'],
            $data['height'],
            $data['x'],
            $data['y']
        )->save();
    }
}
