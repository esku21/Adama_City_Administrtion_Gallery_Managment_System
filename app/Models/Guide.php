<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // This automatically hashes password when saving
        'is_active' => 'boolean',
    ];

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'hall_id', 'hall_id');
    }
}