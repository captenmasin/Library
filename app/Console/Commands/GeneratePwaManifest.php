<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;

class GeneratePwaManifest extends Command
{
    protected $signature = 'app:pwa:generate';

    public function handle(): int
    {
        $contents = json_encode(config('pwa.manifest'));
        $pattern = '/route:([a-zA-Z0-9._]+)/';
        $modifiedContent = preg_replace_callback($pattern, function ($matches) {
            return route($matches[1]);
        }, $contents);

        File::ensureDirectoryExists(public_path('images/pwa'));
        File::copyDirectory(resource_path('images/pwa'), public_path('images/pwa'));

        File::copy(resource_path('js/service-worker.js'), public_path('service-worker.js'));
        //        File::copy(public_path('favicon.ico'), public_path('favicon.ico'));

        file_put_contents(public_path('manifest.json'), $modifiedContent);
        $this->info('Manifest.json file created');

        return 0;
    }
}
