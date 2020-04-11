<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Support\Str;

class IndexControllerTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     */
    public function test_can_retrieve_media(string $field, int $count, array $media): void
    {
        $post = $this->createPostWithMedia($media);

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/{$field}")
            ->assertSuccessful()
            ->assertJsonCount($count);
    }

    public function dataProvider(): array
    {
        return [
            ['media', 2, [
                ['default', $this->getJpgFile()],
                ['default', $this->getJpgFile()],
                ['testing', $this->getIgnoredTextFile()],
            ]],
            ['media_testing', 3, [
                ['testing', $this->getTextFile()],
                ['testing', $this->getTextFile()],
                ['testing', $this->getTextFile()],
            ]],
            ['media_testing_single', 1, [
                ['testing_single', $this->getTextFile()],
                ['testing_single', $this->getTextFile()],
            ]],
        ];
    }

    /** @test */
    public function test_can_retrieve_unattached_media(): void
    {
        $uuid = (string) Str::uuid();

        foreach (range(1, 5) as $_) {
            $this->addMediaTo(TransientModel::make(), $this->getTextFile(), $uuid);
        }

        $this
            ->getJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/undefined/media/media?fieldUuid={$uuid}")
            ->assertSuccessful()
            ->assertJsonCount(5);
    }
}
