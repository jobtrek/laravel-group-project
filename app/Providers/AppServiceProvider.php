<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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
        if (! app()->isProduction()) {
            Model::preventLazyLoading();
        }

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
