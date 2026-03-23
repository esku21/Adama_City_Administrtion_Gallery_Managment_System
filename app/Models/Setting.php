<?php

namespace App\Models; // Fixed: Ensuring this is plural "Models"

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Fixed: Path must include Eloquent

class Setting extends Model
{
    use HasFactory;

    // Explicitly define the table name to avoid any pluralization issues
    protected $table = 'settings';

    // Allow these fields to be updated via the Controller
    protected $fillable = [
        'system_status',
        'feedback_status',
    ];

    /**
     * Cast attributes to native types.
     */
    protected $casts = [
        'system_status' => 'string',
        'feedback_status' => 'string',
    ];
}