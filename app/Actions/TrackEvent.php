<?php

namespace App\Actions;

use Pirsch\Facades\Pirsch;
use Lorisleiva\Actions\Concerns\AsAction;

class TrackEvent
{
    use AsAction;

    public function handle(string $name, ?array $meta): void
    {
        Pirsch::track(
            name: $name,
            meta: $meta,
        );
    }
}
