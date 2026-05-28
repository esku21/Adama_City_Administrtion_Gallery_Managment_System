<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        // Data must strictly match your RegisteredUserController validation rules
        $response = $this->post('/register', [
            'firstName'             => 'Abebe', // Must contain letters and vowels
            'lastName'              => 'Bekele', // Must contain letters and vowels
            'email'                 => 'test@gmail.com', // Must match allowed regex domains
            'phone_no'              => '0911223344',    // Must start with 09 or 07
            'visitorType'           => 'Local',         // Must be 'Local' or 'Foreign'
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        // Your controller redirects to route('login')
        $response->assertRedirect(route('login'));

        // Assert success message is present
        $response->assertSessionHas('success', 'Registration successful! Please login.');

        // Verify the user exists in the database
        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
        ]);
    }
}