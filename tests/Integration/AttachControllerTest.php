<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\TestPost;
use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\Models\Media;

class AttachControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();
    }

    /** @test */
    public function media_are_validated_before_being_attached(): void
    {
        $post = $this->createPost();

        $file = $this->makeUploadedFile($this->getTextFile());

        $this
            ->postJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media_testing_validation")
            ->assertJsonValidationErrors('file');

        $errors = [];

        $this->withoutExceptionHandling();

        try {
            $this->postWithFile("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media_testing_validation", [], $file);
        } catch (ValidationException $exception) {
            $errors = $exception->errors();
        }

        $this->assertSame('The file must be an image.', $errors['file'][0] ?? '');
    }

    /** @test */
    public function media_can_be_attached_to_an_existing_resource(): void
    {
        $post = $this->createPost();

        $file = $this->makeUploadedFile($this->getJpgFile());

        $this
            ->postWithFile("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media_testing", [], $file)
            ->assertStatus(201);

        $this->assertCount(1, $post->media);
    }

    /** @test */
    public function media_can_be_attached_to_a_non_existing_resource(): void
    {
        $file = $this->makeUploadedFile($this->getJpgFile());
        $fieldUuid = (string) Str::uuid();

        TestPost::$withConversions = true;

        // store media
        $this
            ->postWithFile('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/undefined/media/media_testing', [
                'fieldUuid' => $fieldUuid,
            ], $file)
            ->assertStatus(201);

        TestPost::$withConversions = false;

        $media = Media::first();

        $media->setCustomProperty('a', 'b')->save();
        $media->update(['manipulations' => ['*' => ['with' => 100]]]);

        $this->assertSame($fieldUuid, $media->collection_name);
        $this->assertTrue($media->model->is(TransientModel::make()));
        $this->assertCount(1, TransientModel::make()->media);
        $this->assertSame(['preview' => true], $media->getGeneratedConversions()->all());
        $this->assertSame([
            'target' => TestPost::class,
            'collectionName' => 'testing',
        ], $media->getCustomProperty(TransientModel::getCustomPropertyName()));

        // attach all stored media
        $this->postJson('/nova-api/test-posts', ['media_testing' => $fieldUuid])
            ->assertStatus(201);

        $post = TestPost::first();

        $this->assertNull($media->fresh());
        $this->assertCount(0, TransientModel::make()->media);
        $this->assertCount(1, $post->media);
        $this->assertSame('b', $post->media->first()->getCustomProperty('a'));
        $this->assertSame(null, $post->media->first()->getCustomProperty(TransientModel::getCustomPropertyName()));
        $this->assertSame(['*' => ['with' => 100]], $post->media->first()->manipulations);
    }

    /** @test */
    public function existing_media_can_be_attached(): void
    {
        $this->createPostWithMedia();

        $post = $this->createPost();

        $this->withoutExceptionHandling();

        $this
            ->postJson("nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/{$post->id}/media/media_testing", ['media' => 1])
            ->assertStatus(201);

        $this->assertCount(1, $post->media);
    }

    private function postWithFile(string $uri, array $parameters, UploadedFile $file): TestResponse
    {
        return $this->call('POST', $uri, $parameters, [], ['file' => $file]);
    }

    private function makeUploadedFile(string $path): UploadedFile
    {
        return new UploadedFile(
            $path,
            basename($path),
            mime_content_type($path),
            null,
            true,
        );
    }
}
