<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These must match the column names in your MySQL 'images' table.
     */
    protected $fillable = [
        'url',             // Stores the link to the image file
        'title',           // The description/title of the photo
        'views_count',     // Number of times viewed
        'likes_count',     // Total likes (Updated via GalleryController)
        'dislikes_count'   // Total dislikes (Updated via GalleryController)
    ];

    /**
     * Optional: Set default values for counts so they are never NULL
     */
    protected $attributes = [
        'views_count' => 0,
        'likes_count' => 0,
        'dislikes_count' => 0,
    ];
}