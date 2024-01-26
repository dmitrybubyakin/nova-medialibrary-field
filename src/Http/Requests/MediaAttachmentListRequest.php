<?php

namespace DmitryBubyakin\NovaMedialibraryField\Http\Requests;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaAttachmentListData;

class MediaAttachmentListRequest extends MediaRequest
{
    public function rules(): array
    {
        return [
            'perPage' => [
                'sometimes',
                'integer',
                'min:1',
            ],

            'name' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'maxSize' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
            ],

            'mimeType' => [
                'sometimes',
                'nullable',
                'string',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getData(): MediaAttachmentListData
    {
        $resourceExists = $this->resourceExists();

        return MediaAttachmentListData::from([
            'request' => $this,
            'field' => $this->medialibraryField(),
            'resourceModel' => $resourceExists ? $this->findModelOrFail() : null,
            'resourceExists' => $resourceExists,
            'perPage' => $this->get('perPage', 25),
            'name' => $this->get('name'),
            'maxSize' => $this->get('maxSize'),
            'mimeType' => $this->get('mimeType'),
        ]);
    }
}
