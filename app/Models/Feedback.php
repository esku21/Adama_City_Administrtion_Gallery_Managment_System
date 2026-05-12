<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    // Set to true to automatically handle created_at and updated_at
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'booking_id',
        'type',
        'hall_id',
        'subject',
        'message',
        'rating',
        'image_path',
        'sentiment_status',
        'topic_tag',
        'verified_by',
        'resolution_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}