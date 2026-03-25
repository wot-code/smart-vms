<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These correspond to the columns in your migration to prevent MassAssignmentExceptions.
     */
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address'
    ];

    /**
     * The attributes that should be cast.
     * Helpful for Objective 4 (Reporting) to ensure dates are always objects.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Get the user (Admin/Guard/Host) who performed the action.
     * This fulfills Objective 3 by creating a clear chain of accountability.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}