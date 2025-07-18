<?php

use SoloTerm\Solo\Themes;
use SoloTerm\Solo\Hotkeys;
use SoloTerm\Solo\Commands\Command;
use SoloTerm\Solo\Commands\EnhancedTailCommand;

// Solo may not (should not!) exist in prod, so we have to
// check here first to see if it's installed.
if (! class_exists('\SoloTerm\Solo\Manager')) {
    return [
        //
    ];
}

return [
    /*
    |--------------------------------------------------------------------------
    | Themes
    |--------------------------------------------------------------------------
    */
    'theme' => env('SOLO_THEME', 'dark'),

    'themes' => [
        'light' => Themes\LightTheme::class,
        'dark' => Themes\DarkTheme::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Keybindings
    |--------------------------------------------------------------------------
    */
    'keybinding' => env('SOLO_KEYBINDING', 'default'),

    'keybindings' => [
        'default' => Hotkeys\DefaultHotkeys::class,
        'vim' => Hotkeys\VimHotkeys::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    |
    */
    'commands' => [
        'Vite' => 'npm run dev',
        'Queue' => Command::from('php artisan horizon')->lazy(),
        'Logs' => EnhancedTailCommand::file(storage_path('logs/laravel.log')),
        'Scheduler' => Command::from('php artisan schedule:work')->lazy(),

        // 'Dumps' => Command::from('php artisan solo:dumps')->lazy(),
        // 'Reverb' => Command::from('php artisan reverb:start --debug')->lazy(),
        'Tests' => Command::from('APP_ENV=testing vendor/bin/pest --watch --parallel')->withEnv(['APP_ENV' => 'testing'])->lazy(),
        //        'Nightwatch' => Command::from('php artisan nightwatch:agent')->lazy(),
    ],

    /**
     * By default, we prefer to use GNU Screen as an intermediary between Solo
     * and the underlying process. This helps us with many issues, including
     * PTY and some ANSI rendering things. Not all environments have Screen,
     * so you can turn it off for a slightly degraded experience.
     */
    'use_screen' => (bool) env('SOLO_USE_SCREEN', true),

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    /*
     * If you run the solo:dumps command, Solo will start a server to receive
     * the dumps. This is the address. You probably don't need to change
     * this unless the default is already taken for some reason.
     */
    'dump_server_host' => env('SOLO_DUMP_SERVER_HOST', 'tcp://127.0.0.1:9984'),
];
