<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

use Database\Seeders\HallSeeder;
use Database\Seeders\BookingSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // SITE SETTINGS
        try {
            if (Schema::hasTable('site_settings')) {
                DB::table('site_settings')->updateOrInsert(
                    ['key' => 'system_status'],
                    [
                        'value' => 'active',
                        'updated_at' => now(),
                        'created_at' => now()
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        // USERS
        User::updateOrCreate(
            ['email' => 'visitor@example.com'],
            [
                'firstName' => 'Eskedar',
                'lastName' => 'Tesfaye',
                'password' => Hash::make('password'),
                'role' => 'visitor',
                'phone_no' => '0912345678',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@acagms.com'],
            [
                'firstName' => 'Admin',
                'lastName' => 'User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // CALL SEEDERS
        $this->call([
            HallSeeder::class,
            BookingSeeder::class,
        ]);

        $this->command->info("Database seeded successfully 🚀");
    }
}