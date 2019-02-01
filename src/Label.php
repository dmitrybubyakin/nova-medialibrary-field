<?php

namespace DmitryBubyakin\NovaMedialibraryField;

use Spatie\MediaLibrary\Models\Media;

class Label
{
    public $title;
    public $resolver;
    public $trueColor;
    public $falseColor;

    public function __construct(string $title, callable $resolver, ?string $trueColor, ?string $falseColor)
    {
        $this->title = $title;
        $this->resolver = $resolver;
        $this->trueColor = $trueColor;
        $this->falseColor = $falseColor;
    }

    public function resolve(Media $media): array
    {
        $condition = (bool) call_user_func($this->resolver, $media);

        $visible = is_string($this->trueColor) && $condition ||
                is_string($this->falseColor) && ! $condition ||
                is_string($this->trueColor) && is_string($this->falseColor);

        return [
            'title' => $this->title,
            'color' => $condition ? $this->trueColor : $this->falseColor,
            'visible' => $visible,
        ];
    }
}
