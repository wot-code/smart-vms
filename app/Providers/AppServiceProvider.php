<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use App\Models\User; 

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
        /**
         * Define the 'admin-only' security gate.
         * This gate checks if the logged-in user has the 'admin' role.
         */
        Gate::define('admin-only', function (User $user) {
            return $user->role === 'admin';
        });
    }
}