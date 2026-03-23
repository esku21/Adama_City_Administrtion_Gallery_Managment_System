<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

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
        'hall_id', 
        'attachment_path', 
        'status', 
        'qr_token',          
    ];

    protected $casts = [
        'booking_date'       => 'date:Y-m-d',
        'number_of_visitors' => 'integer',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    // These make the fields available in the Vue frontend automatically
    protected $appends = ['hall_names', 'readable_slot'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (empty($booking->qr_token)) {
                $booking->qr_token = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationships
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function halls(): BelongsToMany
    {
        return $this->belongsToMany(Hall::class, 'booking_hall', 'booking_id', 'hall_id');
    }

    /**
     * Accessor: Hall Names
     * Combines many-to-many halls or falls back to single hall_id
     */
    public function getHallNamesAttribute(): string
    {
        // Check if many-to-many relationship is loaded and not empty
        if ($this->relationLoaded('halls') && $this->halls->isNotEmpty()) {
            return $this->halls->pluck('name')->implode(', ');
        }
        
        // Fallback to the single hall relationship
        return $this->hall->name ?? 'General Visit';
    }

    /**
     * Accessor: Readable Slot
     * Maps the slot_id (m1, a1, etc) to a human-readable time range
     */
    public function getReadableSlotAttribute(): string
    {
        $slots = [
            'm1' => '09:00 AM - 10:00 AM',
            'm2' => '10:00 AM - 11:00 AM',
            'm3' => '11:00 AM - 12:00 PM',
            'a1' => '01:00 PM - 02:00 PM',
            'a2' => '02:00 PM - 03:00 PM',
            'a3' => '03:00 PM - 04:00 PM',
        ];

        // If slot_id is null, return 'Not Assigned'
        if (!$this->slot_id) {
            return 'Not Assigned';
        }

        // Return mapped time, or the raw ID if it's a numeric ID instead of a code
        return $slots[$this->slot_id] ?? (string)$this->slot_id;
    }
}