<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * Relationship: The bookings associated with this hall.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'hall_id');
    }

    /**
     * Relationship: The Users (Guides) assigned to this hall.
     * Since your 'User' model has 'hall_id', we fetch users with the 'guide' role.
     */
    public function guides(): HasMany
    {
        return $this->hasMany(User::class, 'hall_id')->where('role', 'guide');
    }

    /**
     * Relationship: Feedbacks specifically for this hall.
     * This is what allows: Auth::user()->hall->feedbacks to work!
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'hall_id');
    }

    /**
     * Scope: Only return halls currently available for booking.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}