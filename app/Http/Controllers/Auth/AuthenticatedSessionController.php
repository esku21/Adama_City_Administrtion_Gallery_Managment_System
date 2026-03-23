<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Validates credentials (email/password)
        $request->authenticate();

        // 2. Protect against session fixation
        $request->session()->regenerate();

        $user = Auth::user();

        // 3. SECURE REDIRECT LOGIC
        // We use strtolower() to make sure "Admin", "ADMIN", and "admin" all work.
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        } 
        
        
        if ($role === 'visitor') {
            return redirect()->intended(route('visitor.dashboard'));
        }

        // If no role matches, send to home
        return redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}