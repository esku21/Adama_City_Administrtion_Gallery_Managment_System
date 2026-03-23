<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. ALWAYS PASS: If the user is an Admin, they bypass everything.
        // This ensures you never get locked out of your own dashboard.
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // 2. Fetch system status from the database.
        $settings = Setting::first();
        $status = $settings->system_status ?? 'active';

        // 3. PASS: If the system is active, proceed normally.
        if ($status === 'active') {
            return $next($request);
        }

        // 4. PASS: Critical routes that must ALWAYS be accessible.
        // We include login/logout so you can sign in to become an admin,
        // and the maintenance page so visitors can actually see the message.
        if ($request->is('login') || 
            $request->is('logout') || 
            $request->is('api/*') || // Optional: allows API calls if needed
            $request->routeIs('login') || 
            $request->routeIs('logout') || 
            $request->routeIs('maintenance.page')) {
            return $next($request);
        }

        // 5. BLOCK: Everyone else is redirected to the maintenance page.
        return redirect()->route('maintenance.page');
    }
}