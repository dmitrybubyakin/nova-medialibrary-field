<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField;

use Illuminate\Support\Collection;

class PruneStaleMedia
{
    private $deleted;

    public function __invoke(): int
    {
        $this->deleted = 0;

        TransientModel::make()->staleMedia()->chunk(100, function (Collection $media): void {
            $media->each->delete();
            $this->deleted += $media->count();
        });

        return $this->deleted;
    }
}
