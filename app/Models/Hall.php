<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: The bookings that belong to the hall.
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_hall')
                    ->withTimestamps();
    }

    /**
     * Relationship: Each hall has its own guides.
     * Updated to point to the Guide model specifically.
     */
    public function guides(): HasMany
    {
        // This links to your 'guides' table using the 'hall_id' column
        return $this->hasMany(Guide::class, 'hall_id');
    }
}