<?php

namespace DmitryBubyakin\NovaMedialibraryField\Http\Requests;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaListData;

class MediaListRequest extends MediaRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function getData(): MediaListData
    {
        $resourceExists = $this->resourceExists();

        return MediaListData::from([
            'field' => $this->medialibraryField(),
            'fieldUuid' => $this->fieldUuid(),
            'resourceModel' => $resourceExists ? $this->findModelOrFail() : null,
            'resourceExists' => $resourceExists,
        ]);
    }
}
