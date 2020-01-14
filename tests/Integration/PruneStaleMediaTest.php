<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Integration;

use DmitryBubyakin\NovaMedialibraryField\PruneStaleMedia;
use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use DmitryBubyakin\NovaMedialibraryField\TransientModel;
use Spatie\MediaLibrary\Models\Media;

class PruneStaleMediaTest extends TestCase
{
    /** @test */
    public function test_prune_stale_media(): void
    {
        foreach (range(1, 10) as $_) {
            TransientModel::make()
                ->addMedia($this->getTextFile())
                ->preservingOriginal()
                ->toMediaCollection();
        }

        Media::take(5)->update(['created_at' => now()->subDay()]);

        call_user_func(new PruneStaleMedia);

        $this->assertCount(5, TransientModel::make()->media);
    }
}
