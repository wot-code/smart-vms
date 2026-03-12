<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Support\Facades\Request; 
use App\Models\User; 
use App\Models\SecurityLog; 

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
         * Only users with the 'admin' role can pass.
         */
        Gate::define('admin-only', function (User $user) {
            if ($user->role === 'admin') {
                return true;
            }

            // Log unauthorized attempts only if they are hitting an admin URL
            if (Request::is('admin/*')) {
                $this->logSecurityAttempt($user, 'UNAUTHORIZED_ADMIN_ACCESS');
            }

            return false;
        });

        /**
         * Define the 'guard-access' security gate.
         * Allows both 'guard' and 'admin' roles.
         */
        Gate::define('guard-access', function (User $user) {
            if (in_array($user->role, ['guard', 'admin'])) {
                return true;
            }

            // Log unauthorized attempts only if they are hitting a guard URL
            if (Request::is('guard/*')) {
                $this->logSecurityAttempt($user, 'UNAUTHORIZED_GUARD_ACCESS');
            }

            return false;
        });
    }

    /**
     * Records who, where, and when someone tried to access restricted areas.
     * Includes an action label for better audit reporting.
     */
    protected function logSecurityAttempt(User $user, string $action): void
    {
        try {
            SecurityLog::create([
                'user_id'    => $user->id,
                'action'     => $action, // Added action column for better filtering
                'url'        => Request::fullUrl(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            // Silently fail to prevent the app from crashing if logging fails
            \Illuminate\Support\Facades\Log::error("Failed to log security attempt: " . $e->getMessage());
        }
    }
}