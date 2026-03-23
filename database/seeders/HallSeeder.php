<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hall;

class HallSeeder extends Seeder
{
    public function run(): void
    {
        $halls = [
            ['id' => 1, 'name' => 'Adama City Hall', 'description' => 'Central hub for city assemblies.'],
            ['id' => 2, 'name' => 'Municipal Council Chamber', 'description' => 'Space for local governance.'],
            ['id' => 3, 'name' => 'Aba Gadaa Cultural Hall', 'description' => 'Cultural and historical venue.'],
            ['id' => 4, 'name' => 'Investment Promotion Hall', 'description' => 'Center for economic growth.'],
            ['id' => 5, 'name' => 'Land Management Hall', 'description' => 'Urban development services.'],
            ['id' => 6, 'name' => 'Multi-Purpose Hall', 'description' => 'Versatile space for gatherings.'],
            ['id' => 7, 'name' => 'Public Relations Hall', 'description' => 'Communication bridge.'],
            ['id' => 8, 'name' => 'Justice & Human Rights Hall', 'description' => 'Legal awareness hall.'],
            ['id' => 9, 'name' => 'Revenue Service Hall', 'description' => 'Central tax services.'],
        ];

        foreach ($halls as $hall) {
            Hall::updateOrCreate(
                ['id' => $hall['id']], 
                [
                    'name' => $hall['name'],
                    'description' => $hall['description'],
                    'location' => 'Main Complex', 
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Halls seeded successfully!');
    }
}