<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = [
            ['url' => '/storage/images/gallery1.jpg', 'title' => 'Adama City Hall'],
            ['url' => '/storage/images/gallery2.jpg', 'title' => 'Adama Expressway'],
            ['url' => '/storage/images/gallery3.jpg', 'title' => 'Adama Industry Park'],
            ['url' => '/storage/images/gallery4.jpg', 'title' => 'Adama Science & Tech'],
            ['url' => '/storage/images/gallery5.jpg', 'title' => 'Adama Stadium'],
            ['url' => '/storage/images/gallery6.jpg', 'title' => 'Adama Night View'],
            
        ];

        foreach ($images as $img) {
            Image::create([
                'url' => $img['url'],
                'title' => $img['title'],
                'views_count' => 0,
                'likes_count' => 0,
            ]);
        }
    }
}