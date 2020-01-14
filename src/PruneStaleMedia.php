<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField;

use Illuminate\Support\Collection;

class PruneStaleMedia
{
    public function __invoke(): void
    {
        TransientModel::make()->staleMedia()->chunk(100, function (Collection $media): void {
            $media->each->delete();
        });
    }
}
