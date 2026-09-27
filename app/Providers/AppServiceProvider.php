<?php

namespace App\Providers;

use App\Auth\FirestoreUserProvider;
use Illuminate\Support\Facades\Auth;
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
        Auth::provider('firestore', function ($app, array $config) {
            return $app->make(FirestoreUserProvider::class);
        });
    }
}