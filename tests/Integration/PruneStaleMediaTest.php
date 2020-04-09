<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\PruneStaleMedia;
use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PruneStaleMediaTest extends TestCase
{
    /** @test */
    public function test_prune_stale_media(): void
    {
        foreach (range(1, 10) as $_) {
            $this->addMediaTo(TransientModel::make(), $this->getTextFile(), 'default');
        }

        Media::take(5)->update(['created_at' => now()->subDay()]);

        $this->assertSame(5, call_user_func(new PruneStaleMedia));
        $this->assertSame(0, call_user_func(new PruneStaleMedia));

        $this->assertCount(5, TransientModel::make()->media);
    }
}
