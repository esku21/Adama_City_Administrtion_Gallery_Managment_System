<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    // Table name is singular based on your migration
    protected $table = 'feedback';

    // Disable updated_at as you requested Option 2
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'booking_id',
        'type',       // 'general' or 'hall'
        'hall_id',    // Links to the specific hall
        'subject',
        'message',
        'rating',     // Star rating (1-5)
        'image_path',
        'created_at', 
    ];

    /**
     * Relationship: The feedback belongs to a visitor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: The feedback belongs to a specific hall.
     */
    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }
}