<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends Model 
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * ✅ FIXED: Added 'user_id' to prevent mass-assignment crashes during booking updates/rejections
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'target_date',
        'reschedule_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     * Ensures dates are returned as Carbon objects for easy formatting.
     */
    protected $casts = [
        'target_date'     => 'date',
        'reschedule_date' => 'date',
        'is_active'       => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Users who have received/read this announcement via the pivot bridge.
     */
    public function users(): BelongsToMany 
    {
        return $this->belongsToMany(User::class, 'announcement_user')
                    ->withPivot('is_read')
                    ->withTimestamps();
    }

    /**
     * Alias method to match your controller naming conventions.
     */
    public function readByUsers(): BelongsToMany 
    {
        return $this->users();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Scope a query to only include active announcements.
     * Usage: Announcement::active()->get();
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}