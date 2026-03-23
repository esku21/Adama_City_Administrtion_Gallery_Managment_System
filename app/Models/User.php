<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * UPDATED: Added 'name', 'phone', 'gender', and 'hall_id'
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'name',        // Added for compatibility with your Vue form
        'email',
        'password',
        'phone_no',
        'phone',       // Added for compatibility with your Vue form
        'gender',      // Added
        'visitorType',
        'citizenship',
        'role',
        'hall_id',     // CRITICAL: Must be here to save the assignment
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['full_name'];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', 
        ];
    }

    /**
     * Accessor for Full Name.
     */
    public function getFullNameAttribute(): string
    {
        // Null safety in case firstName/lastName are empty
        return trim("{$this->firstName} {$this->lastName}") ?: (string)$this->name;
    }

    /**
     * Relationship: A guide belongs to a hall.
     * FIXES: RelationNotFoundException
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }

    /**
     * Relationship: A user can have many bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    /**
     * Relationship: A user can have many feedbacks.
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    /**
     * Helper to check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}