<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'hall_id', 
        'visitor_name',
        'visitor_category',
        'visitor_type',
        'organization_name',
        'number_of_visitors',
        'booking_date',
        'slot_id',
        'attachment', // This is the correct column name
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
     * Fix applied here: Changed attachment_path to attachment
     */
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment 
            ? asset('storage/' . $this->attachment) 
            : null;
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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

        return $slots[strtolower($this->slot_id)] ?? $this->slot_id ?? 'Not Assigned';
    }
}