<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the visitor's profile (Read-Only).
     * Route: route('profile.edit')
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Visitor/Profile', [
            'auth' => [
                'user' => $request->user(),
            ],
        ]);
    }

    /**
     * Display the security settings page (Password Update).
     * Route: route('settings.edit')
     */
    public function settings(Request $request): Response
    {
        return Inertia::render('Visitor/Settings', [
            'auth' => [
                'user' => $request->user(),
            ],
        ]);
    }

    /**
     * Update and Destroy are removed as per your request 
     * to keep the visitor profile information locked/read-only.
     * Password updates are handled by the PasswordController.
     */
}