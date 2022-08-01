<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;

class AttachableControllerTest extends TestCase
{
    /** @test */
    public function test_can_retrieve_attachable_media(): void
    {
        $post = $this->createPostWithMedia(10);

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media/attachable")
            ->assertSuccessful()
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function test_can_retrieve_attachable_media_with_pagination(): void
    {
        $post = $this->createPostWithMedia(10);

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media/attachable?perPage=5")
            ->assertSuccessful()
            ->assertJsonCount(5, 'data');

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media/attachable?perPage=3&page=4")
            ->assertSuccessful()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function test_can_retrieve_attachable_media_with_filtering(): void
    {
        $post = $this->createPostWithMedia(1, 'default', $this->getJpgFile());
        $post = $this->createPostWithMedia(9, 'default', $this->getTextFile());

        $post->media()->take(2)->get()->each->update(['name' => 'xxx']);

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media/attachable?name=xxx")
            ->assertSuccessful()
            ->assertJsonCount(2, 'data');

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media/attachable?maxSize=0")
            ->assertSuccessful()
            ->assertJsonCount(10, 'data');

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media/attachable?mimeType=text/*")
            ->assertSuccessful()
            ->assertJsonCount(9, 'data');

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media/attachable?mimeType=image/*")
            ->assertSuccessful()
            ->assertJsonCount(1, 'data');

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media/attachable?mimeType=image/jpg,image/jpeg,image/png,image/gif")
            ->assertSuccessful()
            ->assertJsonCount(1, 'data');
    }
}
