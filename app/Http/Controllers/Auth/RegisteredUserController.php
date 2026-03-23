<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Visitor/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'firstName'   => 'required|string|max:255',
            'lastName'    => 'required|string|max:255',
            'email'       => 'required|string|lowercase|email|max:255|unique:users,email',
            'phone_no'    => 'required|string|max:20|unique:users,phone_no',
            'visitorType' => 'required|string',
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();

        try {
            // Insert into 'users' table
            $user = User::create([
                'firstName'   => $request->firstName,
                'lastName'    => $request->lastName,
                'email'       => $request->email,
                'phone_no'    => $request->phone_no,
                'visitorType' => $request->visitorType,
                'password'    => Hash::make($request->password), 
                'role'        => 'visitor', 
                'citizenship' => $request->citizenship ?? 'Ethiopian',
            ]);

            // Optional: Insert into 'visitors' table if you use it for history
            Visitor::create([
                'firstName'   => $request->firstName,
                'lastName'    => $request->lastName,
                'email'       => $request->email,
                'phone_no'    => $request->phone_no,
                'visitorType' => $request->visitorType,
            ]);

            DB::commit();

            event(new Registered($user));

            // Redirect to login with a success message
            return redirect()->route('login')->with('status', 'Registration successful! Please log in with your new account.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['email' => 'Registration failed.'])->withInput();
        }
    }
}