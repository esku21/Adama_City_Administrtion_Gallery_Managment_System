<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guide;
use App\Models\Hall;
use Illuminate\Support\Facades\Hash;

class GuideSeeder extends Seeder
{
    public function run(): void
    {
        $halls = Hall::all();

        foreach ($halls as $hall) {
            Guide::updateOrCreate(
                ['email' => "guide" . $hall->id . "@adama.gov.et"],
                [
                    'name'      => "Staff for " . $hall->name,
                    'phone'     => "090000000" . $hall->id,
                    'gender'    => 'Male',
                    'hall_id'   => $hall->id,
                    'password'  => Hash::make('Adama@2026'), // This becomes the hash in DB
                    'is_active' => true,
                ]
            );
        }
    }
}