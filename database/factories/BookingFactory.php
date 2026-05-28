<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Str; // Import this

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? 1,
            'visitor_name' => $this->faker->name(),
            'booking_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            
            // FIX: Generate a random string for the qr_token
            'qr_token' => Str::random(32), 

            'status' => $this->faker->randomElement([
                'pending',
                'confirmed',
                'cancelled'
            ]),

            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}