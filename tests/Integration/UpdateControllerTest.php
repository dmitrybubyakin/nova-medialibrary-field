<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;

class UpdateControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();
    }

    /** @test */
    public function test_can_retrieve_media_update_fields(): void
    {
        $post = $this->createPostWithMedia();

        $media = $post->media->first();

        $response = $this
            ->getJson("nova-api/dmitrybubyakin-nova-medialibrary-media/{$media->id}/update-fields?viaResource=test-posts&viaField=media")
            ->assertSuccessful();

        $this->assertCount(1, $response->decodeResponseJson()['fields']);
        $this->assertSame('file_name', $response->decodeResponseJson()['fields'][0]['attribute']);
    }

    /** @test */
    public function test_can_update_media(): void
    {
        $post = $this->createPostWithMedia();

        $media = $post->media->first();

        $this
            ->putJson("nova-api/dmitrybubyakin-nova-medialibrary-media/{$media->id}?viaResource=test-posts&viaField=media", ['file_name' => 'Testing'])
            ->assertOk();
    }
}
