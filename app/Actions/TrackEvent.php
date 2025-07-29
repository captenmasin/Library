<?php

namespace App\Actions;

use Pirsch\Facades\Pirsch;
use App\Enums\AnalyticsEvent;
use Lorisleiva\Actions\Concerns\AsAction;

class TrackEvent
{
    use AsAction;

    public function handle(AnalyticsEvent $name, ?array $meta): void
    {
        if (! config('services.pirsch.enabled')) {
            return;
        }

        Pirsch::track(
            name: $name->value,
            meta: $meta,
        );
    }
}
