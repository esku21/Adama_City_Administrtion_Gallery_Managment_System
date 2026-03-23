<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        /**
         * 1. Superadmin Override
         * If a user is a superadmin, they pass every authorization check.
         */
        Gate::before(function (User $user, $ability) {
            if (strtolower($user->role ?? '') === 'superadmin') {
                return true;
            }
        });

        /**
         * 2. Admin Gate
         * Used by: ->middleware(['can:access-admin'])
         */
        Gate::define('access-admin', function (User $user) {
            return strtolower($user->role ?? '') === 'admin';
        });

        /**
         * 3. Guide Gate
         * Used by: ->middleware(['can:access-guide'])
         */
        Gate::define('access-guide', function (User $user) {
            return strtolower($user->role ?? '') === 'guide';
        });

        /**
         * 4. Visitor Gate
         * Used by: ->middleware(['can:access-visitor'])
         */
        Gate::define('access-visitor', function (User $user) {
            $role = strtolower($user->role ?? '');
            // Allows 'visitor', 'user', or even 'guest' roles if needed
            return in_array($role, ['visitor', 'user']);
        });
    }
}