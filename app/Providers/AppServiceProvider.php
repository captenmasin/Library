<?php

namespace App\Providers;

use App\Contracts\BookApiServiceInterface;
use App\Services\GoogleBooksService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Inertia\Response;
use Laravel\Dusk\Browser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookApiServiceInterface::class, GoogleBooksService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Model::preventLazyLoading();

        Response::macro('withBreadcrumbs', function ($breadcrumbs) {
            $breadcrumbs = collect($breadcrumbs)->map(function ($url, $name) {
                return [
                    'title' => $name,
                    'href' => $url,
                ];
            })->values();

            return $this->with('breadcrumbs', $breadcrumbs);
        });

        Browser::macro('fullLogout', function () {
            $this->visit('/test-logout')
                ->waitForLocation('/login')
                ->assertPathIs('/login');
        });

        Browser::macro('disableClientSideValidation', function () {
            $this->script('for(var f=document.forms,i=f.length;i--;)f[i].setAttribute("novalidate",i)');

            return $this;
        });
    }
}
