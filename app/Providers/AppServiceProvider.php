<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Inertia\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Model::shouldBeStrict();

        Response::macro('withBreadcrumbs', function ($breadcrumbs) {
            $breadcrumbs = collect($breadcrumbs)->map(function ($url, $name) {
                return [
                    'title' => $name,
                    'href' => $url,
                ];
            })->values();

            return $this->with('breadcrumbs', $breadcrumbs);
        });
    }
}
