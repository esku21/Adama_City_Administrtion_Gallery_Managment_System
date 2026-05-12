<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Note: 'name' is EXCLUDED because it is a virtual column in your migration.
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
        'phone_no',
        'gender',
        'profile_photo_path',
        'visitorType',
        'citizenship',
        'role',
        'hall_id', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Since 'name' is now handled by the database (virtualAs), 
     * we only use $appends if you need a custom logic attribute.
     */
    protected $appends = ['full_name'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', 
        ];
    }

    /* ACCESSORS */

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->firstName} {$this->lastName}") ?: ($this->firstName ?? 'User'),
        );
    }

    /* RELATIONSHIPS */

    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class, 'announcement_user')
                    ->withPivot('is_read')
                    ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }

    /* ROLE HELPERS */

    public function isAdmin(): bool { return strtolower($this->role ?? '') === 'admin'; }
    public function isGuide(): bool { return strtolower($this->role ?? '') === 'guide'; }
    public function isVisitor(): bool { return strtolower($this->role ?? '') === 'visitor'; }
}