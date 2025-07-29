<?php

namespace App\Providers;

use App\Models\User;
use Inertia\Response;
use Laravel\Dusk\Browser;
use App\Services\ISBNdbService;
use Filament\Support\Colors\Color;
use App\Services\GoogleBooksService;
use Illuminate\Support\Facades\Vite;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Contracts\BookApiServiceInterface;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //        $this->app->bind(BookApiServiceInterface::class, GoogleBooksService::class);
        $this->app->bind(BookApiServiceInterface::class, ISBNdbService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Model::preventLazyLoading();

        Vite::prefetch(6);

        FilamentColor::register([
            'primary' => Color::Amber,
        ]);

        Response::macro('withBreadcrumbs', function ($breadcrumbs) {
            $breadcrumbs = collect($breadcrumbs)->map(function ($url, $name) {
                return [
                    'title' => $name,
                    'href' => $url,
                ];
            })->values();

            return $this->with('breadcrumbs', $breadcrumbs);
        });

        Response::macro('withMeta', function ($meta) {
            $meta = (object) $meta;
            $title = $meta->title ?? 'untitled';
            $description = $meta->description ?? '';
            $description = str_replace(["\r", "\n"], '', $description);
            $json = $meta->json ?? '';
            $preload = $meta->preload ?? [];
            //            $image = $meta->image ?? Vite::asset('resources/images/social/default.png');
            $image = $meta->image ?? null;
            $canonical = $meta->url ?? url()->full();

            SEOTools::setTitle($title);
            SEOTools::setCanonical($canonical);
            SEOTools::setDescription($description);
            SEOTools::addImages($image);

            JsonLd::setTitle($title);
            JsonLd::setDescription($description);
            JsonLd::setImages([$image]);

            return $this->with('meta', [
                'title' => SEOMeta::getTitle(),
                'description' => SEOMeta::getDescription(),
            ])->withViewData('meta', [
                'feeds' => $meta->feeds ?? null,
                'title' => SEOMeta::getTitle(),
                'json' => $json,
                'description' => $description,
                'image' => $image,
                'canonical' => $canonical,
                'preload' => $preload,
            ]);
        });

        Browser::macro('fullLogout', function () {
            $this->visit('/test-logout')
                ->waitForLocation('/login')
                ->assertPathIs('/login');

            return $this;
        });

        Browser::macro('disableClientSideValidation', function () {
            $this->script('for(var f=document.forms,i=f.length;i--;)f[i].setAttribute("novalidate",i)');

            return $this;
        });

        Browser::macro('loginFully', function (User $user) {
            $this->loginAs($user)
                ->visit('/sanctum/csrf-cookie');

            return $this;
        });
    }
}
