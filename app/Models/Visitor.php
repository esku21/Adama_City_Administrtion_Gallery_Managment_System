<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Visitor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Since your migration used $table->id(), 
     * Laravel expects 'id'. If you want to use 'visitorId', 
     * you must change the migration. 
     * For now, I'll match it to your migration:
     */
    protected $primaryKey = 'id'; 

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'phone_no',
        'visitorType',
        'citizenship',
        'password', // Optional if visitors don't log in directly
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relationship with Bookings
     */
    public function bookings()
    {
        // Matches the 'id' on this table to 'visitor_id' on the bookings table
        return $this->hasMany(Booking::class, 'visitor_id', 'id');
    }
}