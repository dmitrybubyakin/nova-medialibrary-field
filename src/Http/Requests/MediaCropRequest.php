<?php

namespace DmitryBubyakin\NovaMedialibraryField\Http\Requests;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaCropData;
use Illuminate\Foundation\Http\FormRequest;

class MediaCropRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'conversion' => [
                'required',
                'string',
            ],

            'width' => [
                'required',
                'integer',
            ],

            'height' => [
                'required',
                'integer',
            ],

            'x' => [
                'required',
                'integer',
            ],

            'y' => [
                'required',
                'integer',
            ],

            'rotate' => [
                'required',
                'integer',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getData(): MediaCropData
    {
        return MediaCropData::from([
            'mediaId' => $this->route('media'),
            'conversion' => $this->get('conversion'),
            'cropWidth' => $this->get('width'),
            'cropHeight' => $this->get('height'),
            'cropX' => $this->get('x'),
            'cropY' => $this->get('y'),
            'rotate' => $this->get('rotate'),
        ]);
    }
}
