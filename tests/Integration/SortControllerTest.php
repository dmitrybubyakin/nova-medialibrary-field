<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SortControllerTest extends TestCase
{
    /** @test */
    public function media_can_be_sorted(): void
    {
        $this->createPostWithMedia(3);

        $this->assertSame([1 => 1, 2 => 2, 3 => 3], Media::pluck('id', 'order_column')->all());

        $this->postJson('nova-vendor/dmitrybubyakin/nova-medialibrary-field/sort', [
            'media' => [3, 2, 1],
        ]);
        
        $this->assertSame([1 => 3, 2 => 2, 3 => 1], Media::pluck('id', 'order_column')->all());
    }
}
