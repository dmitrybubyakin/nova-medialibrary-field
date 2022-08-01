<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\TestPost;
use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use Illuminate\Support\Facades\File;

class RegenerateControllerTest extends TestCase
{
    public function tearDown(): void
    {
        TestPost::$withConversions = false;

        parent::tearDown();
    }

    /** @test */
    public function it_can_regenerate_media(): void
    {
        $this->createPostWithMedia();

        File::deleteDirectory(storage_path('app/public/1/conversions'));

        TestPost::$withConversions = true;

        $this
            ->postJson('nova-vendor/dmitrybubyakin/nova-medialibrary-field/1/regenerate')
            ->assertOk();

        $this->assertTrue(File::exists(storage_path('app/public/1/conversions/test-preview.jpg')));
    }
}
