<?php

namespace DmitryBubyakin\NovaMedialibraryField\Http\Requests;

use DmitryBubyakin\NovaMedialibraryField\Data\MediaSortData;
use Illuminate\Foundation\Http\FormRequest;

class MediaSortRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'media' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'media.*' => [
                'required',
                'integer',
                'exists:' . config('media-library.media_model') . ',id',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getData(): MediaSortData
    {
        return MediaSortData::from([
            'ids' => $this->input('media', []),
        ]);
    }
}
