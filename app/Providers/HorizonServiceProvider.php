<?php

namespace App\Providers;

use Laravel\Horizon\Horizon;
use App\Enums\UserPermission;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            if (request()->bearerToken() && request()->bearerToken() === config('services.horizon.token')) {
                return true;
            }

            return $user && $user->can(UserPermission::VIEW_HORIZON_PANEL);
        });
    }
}
