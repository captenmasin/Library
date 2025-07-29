<?php

namespace App\Console\Commands;

use App\Support\Robots;
use Illuminate\Console\Command;

class GenerateRobotsTxt extends Command
{
    protected $signature = 'app:robots:generate';

    public function handle(): int
    {
        $robots = new Robots;

        $robots->addUserAgent('*');

        if ($robots->shouldIndex()) {
            //            foreach (config('site.robots.disabled_paths') as $path) {
            //                if (! empty($path)) {
            //                    $robots->addDisallow('/'.$path);
            //                }
            //            }

            $robots->addAllow('/');
            $robots->addSitemap(config('app.url').'/sitemap.xml');
            $robots->addSitemap(config('app.url').'/sitemap_books.xml');
        } else {
            $robots->addDisallow('/');
        }

        file_put_contents(public_path('robots.txt'), $robots->generate());
        $this->info('Robots.txt file created');

        return 0;
    }
}
