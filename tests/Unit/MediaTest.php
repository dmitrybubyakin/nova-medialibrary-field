<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Unit;

use DmitryBubyakin\NovaMedialibraryField\Resources\Media as MediaResource;
use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use Spatie\MediaLibrary\Models\Media;

class MediaTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('medialibrary.media_model', TestMedia::class);
    }

    /** @test */
    public function it_uses_media_model_from_the_config(): void
    {
        $this->assertSame(config('medialibrary.media_model'), TestMedia::class);
        $this->assertSame(TestMedia::class, MediaResource::$model);
    }
}

class TestMedia extends Media
{
    public $table = 'media';
}
