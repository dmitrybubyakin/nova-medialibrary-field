<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TestPost extends Model implements HasMedia
{
    use InteractsWithMedia;

    public static $withConversions = false;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('testing_single')
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        if (! static::$withConversions) {
            return;
        }

        $this->addMediaConversion('preview')
            ->width(368)
            ->height(232);
    }
}
