<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class CheckSystemStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. SAFETY: Skip if console or table missing
        if (app()->runningInConsole() || !Schema::hasTable('site_settings')) {
            return $next($request);
        }

        // 2. BYPASS LIST: Routes that MUST always work
        $isBypassRoute = $request->is('login', 'logout', 'maintenance', 'force-admin-login', 'fix-admin') || 
                         $request->routeIs('login', 'logout', 'maintenance.page');

        if ($isBypassRoute) {
            return $next($request);
        }

        // 3. ADMIN BYPASS (CRITICAL FIX)
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->id == 2 || strtolower($user->role ?? '') === 'admin' || $user->email === 'admin@acagms.com') {
                return $next($request);
            }
        }

        // 4. FETCH SYSTEM STATUS
        $status = DB::table('site_settings')
            ->where('key', 'system_status')
            ->value('value') ?? 'active';

        // 5. IF OFFLINE: Handle Inertia vs Standard Requests
        if ($status !== 'active') {
            // If it's an Inertia request, we must return a 403 or 503 error 
            // so the frontend doesn't crash with "null component"
            if ($request->header('X-Inertia')) {
                abort(503, 'System Offline');
            }

            return redirect()->route('maintenance.page');
        }

        return $next($request);
    }
}