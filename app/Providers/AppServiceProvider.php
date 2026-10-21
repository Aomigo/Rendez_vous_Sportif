<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Vite::prefetch(concurrency: 3);

        View::composer('layouts.navigation', function ($view) {
            $user = Auth::user();

            $view->with([
                'navOrganizations' => $user ? $user->organizations()->orderBy('name')->get() : collect(),
                'currentOrganization' => $user?->currentOrganization(),
            ]);
        });
    }
}
