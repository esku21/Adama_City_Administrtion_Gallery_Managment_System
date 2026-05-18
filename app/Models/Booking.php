<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
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
     * Get the accessible URL for uploaded files
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment 
            ? asset('storage/' . $this->attachment) 
            : null;
    }

    /**
     * Singular Alias for Halls to prevent "Call to undefined relationship [hall]" errors.
     */
    public function hall(): BelongsToMany
    {
        return $this->halls();
    }

    /**
     * Maps a single booking bundle to its selected halls.
     * Includes access to the specific guide assigned to the hall for this booking.
     */
    public function halls(): BelongsToMany
    {
        // Note: Ensure your 'booking_hall_guide' table migration has $table->timestamps() for ->withTimestamps() to function.
        return $this->belongsToMany(Hall::class, 'booking_hall_guide', 'booking_id', 'hall_id')
                    ->withPivot('guide_id')
                    ->withTimestamps();
    }

    /**
     * Singular Alias for Guides to prevent accidental naming issues.
     */
    public function guide(): BelongsToMany
    {
        return $this->guides();
    }

    /**
     * Maps a single booking bundle to its respective guides.
     * Includes access to the specific hall the guide is managing for this booking.
     */
    public function guides(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'booking_hall_guide', 'booking_id', 'guide_id')
                    ->withPivot('hall_id')
                    ->withTimestamps();
    }

    /**
     * Relationship to the visitor/user who created the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor for parsing active timetable slot codes into readable times
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