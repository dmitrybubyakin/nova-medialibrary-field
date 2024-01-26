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

        $mediaIds = Media::query()
            ->pluck('order_column', 'id')
            ->all();

        $this->assertEquals([
            1 => 1,
            2 => 2,
            3 => 3,
        ], $mediaIds);

        $endpoint = 'nova-vendor/dmitrybubyakin/nova-medialibrary-field/sort';

        $data = [
            'media' => [
                3, 2, 1,
            ],
        ];

        $this
            ->postJson($endpoint, $data)
            ->assertOk();

        $mediaIds = Media::query()
            ->pluck('order_column', 'id')
            ->all();

        $this->assertEquals([
            1 => 3,
            2 => 2,
            3 => 1,
        ], $mediaIds);
    }
}
