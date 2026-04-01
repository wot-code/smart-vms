<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Support\Facades\Request; 
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator; // REQUIRED for clean pagination
use App\Models\User; 
use App\Models\SecurityLog; 
use Livewire\Livewire;
use App\Livewire\VisitorRegistrationForm;

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
         * 1. UI FIX: BOOTSTRAP PAGINATION
         * This ensures the ->links() in your dashboard are styled correctly.
         */
        Paginator::useBootstrapFive();

        /**
         * 2. MANUAL LIVEWIRE REGISTRATION
         * Forces the 'visitor-registration-form' string to link to the PHP class.
         */
        if (class_exists(Livewire::class)) {
            Livewire::component('visitor-registration-form', VisitorRegistrationForm::class);
        }

        /**
         * 3. Define the 'admin-only' security gate.
         */
        Gate::define('admin-only', function (User $user) {
            if ($user->role === 'admin') {
                return true;
            }

            // Log unauthorized attempts
            if (Request::is('admin/*')) {
                self::logSecurityAttempt($user, 'UNAUTHORIZED_ADMIN_ACCESS');
            }

            return false;
        });

        /**
         * 4. Define the 'guard-access' security gate.
         */
        Gate::define('guard-access', function (User $user) {
            // Both Guards and Admins should be able to access guard screens
            if (in_array($user->role, ['guard', 'admin'])) {
                return true;
            }

            // Log unauthorized attempts
            if (Request::is('guard/*')) {
                self::logSecurityAttempt($user, 'UNAUTHORIZED_GUARD_ACCESS');
            }

            return false;
        });
    }

    /**
     * Records security attempts.
     * Made this method 'static' so it can be safely called inside the Gate closures above.
     */
    protected static function logSecurityAttempt(User $user, string $action): void
    {
        try {
            // Check if model exists before attempting to write
            if (class_exists(SecurityLog::class)) {
                SecurityLog::create([
                    'user_id'    => $user->id,
                    'action'     => $action,
                    'url'        => Request::fullUrl(),
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::header('User-Agent'),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to log security attempt: " . $e->getMessage());
        }
    }
}