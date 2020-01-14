<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class TestPost extends Model implements HasMedia
{
    use HasMediaTrait;

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
