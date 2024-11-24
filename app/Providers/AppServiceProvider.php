<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserDeletionService; // Import the UserDeletionService class

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserDeletionService::class, function ($app) {
            return new UserDeletionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
