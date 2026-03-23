<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                $role = strtolower($user->role ?? '');

                // Redirect based on role to avoid 403 Forbidden errors
                if ($role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                
                if ($role === 'guide') {
                    return redirect()->route('guide.dashboard');
                }

                return redirect()->route('visitor.dashboard');
            }
        }

        return $next($request);
    }
}