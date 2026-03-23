<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class GuideLoginController extends Controller
{
    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/GuideLogin', [
            'halls' => Hall::all(),
            'status' => session('status'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'hall_id' => 'required|exists:halls,id',
        ]);

        // Use 'guide' guard
        if (Auth::guard('guide')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            
            $guide = Auth::guard('guide')->user();

            // Check if they chose the correct Hall in the dropdown
            if ((int)$guide->hall_id !== (int)$request->hall_id) {
                Auth::guard('guide')->logout();
                throw ValidationException::withMessages([
                    'hall_id' => 'This account is not assigned to the selected hall.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('guide.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('guide')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}