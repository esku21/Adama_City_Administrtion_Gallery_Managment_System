<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hall extends Model
{
    use HasFactory;

    protected $table = 'halls';

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Plural relationship to bookings via the custom pivot table.
     * ✅ FIXED: Explicitly links to booking_hall_guide to match Booking.php
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_hall_guide', 'hall_id', 'booking_id')
                    ->withPivot('guide_id')
                    ->withTimestamps();
    }

    /**
     * Singular alias to prevent broken connection exceptions if called singularly.
     */
    public function booking(): BelongsToMany
    {
        return $this->bookings();
    }

    /**
     * Relationship to Feedbacks submitted for this specific hall.
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'hall_id');
    }
}