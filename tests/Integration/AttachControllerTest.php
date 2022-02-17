<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\TestPost;
use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AttachControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();

        TestResponse::macro('assertJsonValidationErrorMessage', function (string $key, string $message) {
            $this->assertJsonValidationErrors($key);

            $messages = $this->json()['errors'][$key] ?? [];

            PHPUnit::assertContains($message, $messages);

            return $this;
        });
    }

    /** @test */
    public function media_can_be_validated_before_being_attached_to_an_existing_resource(): void
    {
        $post = $this->createPost();

        $uri = "nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media_testing_validation";

        $this
            ->postJson($uri)
            ->assertJsonValidationErrorMessage('file', 'The file field is required.');

        $file = $this->makeUploadedFile($this->getTextFile());

        $this
            ->postJson($uri, ['file' => $file])
            ->assertJsonValidationErrorMessage('file', 'The file must be an image.');

        $file = $this->makeUploadedFile($this->getJpgFile());

        $this
            ->postJson($uri, ['file' => $file])
            ->assertCreated();
    }

    /** @test */
    public function media_can_be_validated_before_being_attached_to_a_non_existing_resource(): void
    {
        $uuid = (string) Str::uuid();

        $uri = 'nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/undefined/media/media_testing_validation';

        $this
            ->postJson($uri, ['fieldUuid' => $uuid])
            ->assertJsonValidationErrorMessage('file', 'The file field is required.');

        $file = $this->makeUploadedFile($this->getTextFile());

        $this
            ->postJson($uri, ['fieldUuid' => $uuid, 'file' => $file])
            ->assertJsonValidationErrorMessage('file', 'The file must be an image.');

        $file = $this->makeUploadedFile($this->getJpgFile());

        $this
            ->postJson($uri, ['fieldUuid' => $uuid, 'file' => $file])
            ->assertCreated();
    }

    /** @test */
    public function media_collection_can_be_validated_before_resource_creation(): void
    {
        $uuid = (string) Str::uuid();

        $this
            ->postJson('/nova-api/test-posts', ['media_testing_custom_attribute' => $uuid])
            ->assertJsonValidationErrorMessage('media_testing_custom_attribute', 'The media_testing_custom_attribute field is required.');

        $this->addMediaTo(TransientModel::make(), $this->getJpgFile(), $uuid);

        $this
            ->postJson('/nova-api/test-posts', ['media_testing_custom_attribute' => $uuid])
            ->assertCreated();
    }

    /** @test */
    public function media_collection_can_be_validated_before_resource_update(): void
    {
        $uuid = (string) Str::uuid();

        $post = $this->createPostWithMedia(1, 'testing');

        $this
            ->putJson("/nova-api/test-posts/{$post->id}", ['media_testing_custom_attribute' => $uuid])
            ->assertJsonValidationErrorMessage('media_testing_custom_attribute', 'The media_testing_custom_attribute must have at least 2 items.');

        $post = $this->createPostWithMedia(2, 'testing');

        $this
            ->putJson("/nova-api/test-posts/{$post->id}", ['media_testing_custom_attribute' => $uuid])
            ->assertOk();
    }

    /** @test */
    public function media_can_be_attached_to_an_existing_resource(): void
    {
        $post = $this->createPost();

        $file = $this->makeUploadedFile($this->getJpgFile());

        $this
            ->postJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media_testing_custom_attribute", ['file' => $file])
            ->assertCreated();

        $this->assertCount(1, $post->media);
    }

    /** @test */
    public function media_can_be_attached_to_a_non_existing_resource(): void
    {
        $uuid = (string) Str::uuid();

        $file = $this->makeUploadedFile($this->getJpgFile());

        TestPost::$withConversions = true;

        $this
            ->postJson('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/undefined/media/media_testing_custom_attribute', [
                'file' => $file,
                'fieldUuid' => $uuid,
            ])
            ->assertCreated();

        TestPost::$withConversions = false;

        $media = Media::first();
        $media->setCustomProperty('a', 'b')->save();
        $media->update(['manipulations' => ['*' => ['width' => 100]]]);

        $this->assertSame($uuid, $media->collection_name);
        $this->assertTrue($media->model->is(TransientModel::make()));
        $this->assertCount(1, TransientModel::make()->media);
        $this->assertSame(['preview' => true], $media->getGeneratedConversions()->all());
        $this->assertSame([
            'target' => TestPost::class,
            'collectionName' => 'testing',
        ], $media->getCustomProperty(TransientModel::getCustomPropertyName()));

        TestPost::$withConversions = true;

        $this
            ->postJson('/nova-api/test-posts', ['media_testing_custom_attribute' => $uuid])
            ->assertCreated();

        TestPost::$withConversions = false;

        $post = TestPost::first();

        $this->assertNull($media->fresh());
        $this->assertCount(0, TransientModel::make()->media);
        $this->assertCount(1, $post->media);
        $this->assertSame('b', $post->media->first()->getCustomProperty('a'));
        $this->assertSame(null, $post->media->first()->getCustomProperty(TransientModel::getCustomPropertyName()));
        $this->assertSame(['*' => ['width' => 100]], $post->media->first()->manipulations);
    }

    /** @test */
    public function existing_media_can_be_attached_to_an_existing_resource(): void
    {
        $this->createPostWithMedia();

        $post = $this->createPost();

        $this
            ->postJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media_testing_custom_attribute", ['media' => 1])
            ->assertCreated();

        $this->assertCount(1, $post->media);
    }

    /** @test */
    public function existing_media_can_be_attached_to_a_non_existing_resource_with_single_file_collection(): void
    {
        $uuid = (string) Str::uuid();

        $this->createPostWithMedia();

        $this
            ->postJson('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/undefined/media/media_testing_single', [
                'media' => 1,
                'fieldUuid' => $uuid,
            ])
            ->assertCreated();

        $this
            ->postJson('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/undefined/media/media_testing_single', [
                'media' => 1,
                'fieldUuid' => $uuid,
            ])
            ->assertCreated();

        $this->assertCount(1, TransientModel::make()->media);
    }

    private function makeUploadedFile(string $path): UploadedFile
    {
        return new UploadedFile(
            $path,
            basename($path),
            null,
            null,
            true
        );
    }
}
