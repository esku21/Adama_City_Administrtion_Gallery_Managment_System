<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\User;
use App\Models\Hall;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ReportTestSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        $hall = Hall::first();

        if (!$user || !$hall) return;

        // MATCHING YOUR CONTROLLER STRINGS
        $sentiments = ['GoodFeedback', 'BadFeedback', 'Neutral'];

        // Seed 20 Bookings for TODAY so they show on the "Weekly" chart
        for ($i = 0; $i < 20; $i++) {
            Booking::create([
                'user_id' => $user->id,
                'hall_id' => $hall->id,
                'visitor_name' => 'Seeded Visitor ' . $i,
                'visitor_category' => 'VIP',
                'visitor_type' => 'Official',
                'number_of_visitors' => rand(1, 5),
                'booking_date' => Carbon::now(), // Sets date to today
                'status' => 'visited',
                'qr_token' => 'AC-' . strtoupper(Str::random(5)),
                'slot_id' => 'm' . rand(1, 5),
            ]);
        }

        // Seed 10 Bad Feedbacks to make that specific chart work
        for ($j = 0; $j < 10; $j++) {
            Feedback::create([
                'user_id' => $user->id,
                'hall_id' => $hall->id,
                'type' => 'hall',
                'subject' => 'Issue reported',
                'message' => 'The facility was not clean today.',
                'rating' => 1,
                'sentiment_status' => 'BadFeedback', 
                'topic_tag' => 'Facilities',
                'created_at' => Carbon::now(),
            ]);
        }
    }
}