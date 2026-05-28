<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_account(): void
    {
        // Use 'visitor' role to match your middleware 'can:access-visitor'
        $user = User::factory()->create(['role' => 'visitor']);
        
        // Ensure you are hitting the correct URI '/visitor/profile'
        $response = $this->actingAs($user)->delete('/visitor/profile', [
            'password' => 'password',
        ]);

        // Assert redirect to home and that the user is now a guest
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}