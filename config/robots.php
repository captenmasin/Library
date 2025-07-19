<?php

return [
    'enabled' => config('app.env') === 'production' || env('ROBOTS_ENABLED', false),
    'disabled_paths' => explode(',', env('ROBOTS_DISABLED_PATHS', '')),
];
