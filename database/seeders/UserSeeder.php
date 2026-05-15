//<?php
//
// namespace Database\Seeders;
//
// use App\Models\User;
// use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\Hash;
//
// class UserSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         // 1. Create Admin Account
//         User::create([
//             'firstName'         => 'Adama',
//             'lastName'          => 'Admin',
//             'email'             => 'admin@adama.gov.et',
//             'password'          => 'password', // Hashed via Model Cast in User.php
//             'phone_no'          => '0911001122',
//             'role'              => 'admin',
//             'citizenship'       => 'Ethiopian',
//             'email_verified_at' => now(), // CRITICAL: Fixes 403 Forbidden error
//         ]);
//
//         // 2. Create Visitor Account
//         User::create([
//             'firstName'         => 'Test',
//             'lastName'          => 'Visitor',
//             'email'             => 'visitor@example.com',
//             'password'          => 'password',
//             'phone_no'          => '0922334455',
//             'role'              => 'visitor',
//             'visitorType'       => 'local',
//             'citizenship'       => 'Ethiopian',
//             'email_verified_at' => now(), // CRITICAL: Fixes 403 Forbidden error
//         ]);
//
//         // 3. Create Guide Account
//         User::create([
//             'firstName'         => 'Official',
//             'lastName'          => 'Guide',
//             'email'             => 'guide@example.com',
//             'password'          => 'password',
//             'phone_no'          => '0933445566',
//             'role'              => 'guide',
//             'citizenship'       => 'Ethiopian',
//             'email_verified_at' => now(), // CRITICAL: Fixes 403 Forbidden error
//         ]);
//     }
// }
