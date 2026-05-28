<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        // Managed via the booking_hall_guide pivot table
        'visitor_name',
        'visitor_category',
        'visitor_type',
        'organization_name',
        'number_of_visitors',
        'booking_date',
        'slot_id',
        'attachment',
        'status',
        'attended_at',
        'qr_token',
    ];

    protected $casts = [
        'booking_date'       => 'date',
        'attended_at'        => 'datetime',
        'number_of_visitors' => 'integer',
    ];

    protected $appends = ['attachment_url', 'readable_slot'];

    /**
     * Get the accessible URL for files
     */
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment 
            ? asset('storage/' . $this->attachment) 
            : null;
    }

    /**
     * Maps a single booking bundle to its selected halls.
     */
    public function halls(): BelongsToMany
    {
        return $this->belongsToMany(Hall::class, 'booking_hall_guide', 'booking_id', 'hall_id')
                    ->withTimestamps();
    }

    /**
     * Backward compatibility fallback for the singular 'hall' relation call.
     * This safely points calls looking for 'hall' directly to the 'halls' relationship proxy.
     */
    public function hall(): BelongsToMany
    {
        return $this->halls();
    }

    /**
     * Maps a single booking bundle to its respective guides.
     */
    public function guides(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'booking_hall_guide', 'booking_id', 'guide_id')
                    ->withTimestamps();
    }

    /**
     * Relationship to the user who created the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor for parsing active timetable slot codes
     */
    public function getReadableSlotAttribute(): string
    {
        $slots = [
            'm1' => '09:00 AM - 09:30 AM',
            'm2' => '10:00 AM - 10:30 AM',
            'm3' => '11:00 AM - 11:30 AM',
            'a1' => '02:00 PM - 02:30 PM',
            'a2' => '03:00 PM - 03:30 PM',
            'a3' => '04:00 PM - 04:30 PM',
        ];

        return $slots[strtolower($this->slot_id ?? '')] ?? $this->slot_id ?? 'Not Assigned';
    }
}