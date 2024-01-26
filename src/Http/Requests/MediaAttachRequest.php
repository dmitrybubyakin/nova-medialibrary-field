<?php

namespace DmitryBubyakin\NovaMedialibraryField\Http\Requests;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaAttachData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class MediaAttachRequest extends MediaRequest
{
    public function rules(): array
    {
        $field = $this->medialibraryField();

        return [
            'file' => $field->getAttachRules($this),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('media')) {
            return;
        }

        /** @var Model $model */
        $model = config('media-library.media_model');

        /** @var Media $media */
        $media = $model::query()->findOrFail($this->get('media'));

        $temporaryFileBasePath = TemporaryDirectory::create()->path('/');
        $temporaryFilePath = implode('', [
            $temporaryFileBasePath,
            DIRECTORY_SEPARATOR,
            $media->file_name,
        ]);

        app(Filesystem::class)->copyFromMediaLibrary($media, $temporaryFilePath);

        $uploadedFile = new UploadedFile(
            $temporaryFilePath,
            $media->file_name,
            $media->mime_type,
            null,
            true
        );

        $this->merge([
            'file' => $uploadedFile,
        ]);
    }

    public function getData(): MediaAttachData
    {
        $resourceExists = $this->resourceExists();

        return MediaAttachData::from([
            'field' => $this->medialibraryField(),
            'fieldUuid' => $this->fieldUuid(),
            'resourceModel' => $resourceExists ? $this->findModelOrFail() : null,
            'resourceExists' => $resourceExists,
            'resource' => $this->resource(),
            'file' => $this->file('file', $this->input('file')),
        ]);
    }
}
