<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate; // CRITICAL: Added this import

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
        // 1. Safe logic for Site Settings
        if (!app()->runningInConsole()) {
            try {
                if (Schema::hasTable('site_settings')) {
                    $status = DB::table('site_settings')
                        ->where('key', 'system_status')
                        ->value('value');
                    
                    View::share('systemStatus', $status ?? 'active');
                } else {
                    View::share('systemStatus', 'active');
                }
            } catch (\Exception $e) {
                View::share('systemStatus', 'active');
            }
        }

        // 2. DEFINE GATES (Fixes the 403 Forbidden Error)
        
        // This gate handles ->middleware('can:access-admin')
        Gate::define('access-admin', function (User $user) {
            return strtolower($user->role) === 'admin';
        });

        // This gate handles ->middleware('can:access-visitor')
        Gate::define('access-visitor', function (User $user) {
            return in_array(strtolower($user->role), ['visitor', 'admin']);
        });

        // This gate handles ->middleware('can:access-guide')
        Gate::define('access-guide', function (User $user) {
            return strtolower($user->role) === 'guide' || strtolower($user->role) === 'admin';
        });
    }
}