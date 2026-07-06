<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

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
        Gate::before(function ($user, $ability) {
            if (! $user || ! method_exists($user, 'hasPermissionTo')) {
                return null;
            }

            try {
                return $user->hasPermissionTo('manage everything') ? true : null;
            } catch (PermissionDoesNotExist) {
                return null;
            }
        });
    }
}
