<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\TestPost;
use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use Spatie\MediaLibrary\Models\Media;

class CropControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        TestPost::$withConversions = true;
    }

    public function tearDown(): void
    {
        TestPost::$withConversions = false;

        parent::tearDown();
    }

    /** @test */
    public function media_can_be_cropped(): void
    {
        $this->createPostWithMedia([
            ['preview', $this->getJpgFile()],
        ]);

        $data = [
            'conversion' => 'preview',
            'x' => 100,
            'y' => 100,
            'width' => 100,
            'height' => 100,
            'rotate' => 180,
        ];

        $this
            ->postJson('nova-vendor/dmitrybubyakin/nova-medialibrary-field/1/crop', $data)
            ->assertOk();

        $this->assertSame([
            'preview' => [
                'manualCrop' => '100,100,100,100',
                'orientation' => '180',
            ],
        ], Media::first()->manipulations);
    }
}
