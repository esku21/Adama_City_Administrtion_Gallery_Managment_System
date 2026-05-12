<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guide extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'guides';

    protected $fillable = [
        'name',
        'email',
        'gender',
        'phone',
        'password',
        'hall_id',
        'profile_image',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['profile_photo_url'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Accessor for the profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_image 
            ? asset('storage/' . $this->profile_image) 
            : null;
    }

    /**
     * Get the hall assigned to the guide.
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }

    /**
     * Get bookings associated with the hall this guide manages.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'hall_id', 'hall_id');
    }

    /**
     * FIXED: Explicitly mapping 'user_id' to match your database table 'announcement_user'.
     * This stops Laravel from searching for the non-existent 'guide_id'.
     */
    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(
            Announcement::class, 
            'announcement_user', // Pivot table name
            'user_id',           // Foreign key on pivot (from your screenshot)
            'announcement_id'    // Related key on pivot
        )
        ->withPivot('is_read')
        ->withTimestamps();
    }
}