<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Initialize Site Settings
        // We use a try-catch to bypass MySQL Engine Error 1932 if the table is corrupted
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
                $this->command->info('Site settings initialized.');
            } else {
                $this->command->warn('Table "site_settings" not found. Skipping settings seed.');
            }
        } catch (\Exception $e) {
            $this->command->error('MySQL Engine Error on site_settings: ' . $e->getMessage());
            Log::error('Seeder failed for site_settings: ' . $e->getMessage());
        }

        // 2. Create a Test Visitor User
        User::updateOrCreate(
            ['email' => 'visitor@example.com'],
            [
                'firstName'         => 'Eskedar',
                'lastName'          => 'Tesfaye',
                'password'          => Hash::make('password'),
                'role'              => 'visitor',
                'visitorType'       => 'General',
                'citizenship'       => 'Ethiopian',
                'phone_no'          => '0912345678',
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('Visitor user created: visitor@example.com');

        // 3. Create an Admin User
        User::updateOrCreate(
            ['email' => 'admin@acagms.com'],
            [
                'firstName'         => 'Admin',
                'lastName'          => 'User',
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'visitorType'       => 'Staff',
                'citizenship'       => 'Ethiopian',
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('Admin user created: admin@acagms.com');

        // 4. Call Specialized Seeders
        if (class_exists(HallSeeder::class)) {
            $this->call([
                HallSeeder::class,
            ]);
        }

        $this->command->info('--- Database seeding process finished ---');
    }
}