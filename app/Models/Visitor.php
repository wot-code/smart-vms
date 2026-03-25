<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Prunable;

class Visitor extends Model
{
    use HasFactory, Prunable;

    /**
     * Get the prunable model query.
     * DPA Compliance: Automatically delete visitors older than 90 days.
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(90));
    }

    /**
     * The attributes that are mass assignable.
     * I have added all fields from your registration form 
     * and standardized the timestamp columns.
     */
    protected $fillable = [
        'full_name',
        'phone',
        'id_number',
        'type',           // Adult or Minor
        'guardian_name',  // Used for Minors
        'host_id',        // Foreign key to User
        'host_name',      // String fallback for host name
        'purpose',
        'purpose_other',  // Added: for when "Other" is selected
        'vehicle_reg',    
        'signature',      // Base64 string from canvas
        'status',         // pending, approved, checked_in, checked_out
        'checked_in_at',  // Standardized from your migration
        'checked_out_at'  // Standardized from your migration
    ];

    /**
     * The attributes that should be cast.
     * This allows you to do $visitor->checked_in_at->format('H:i')
     */
    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    /**
     * Relationship: Visitor belongs to a Host (User).
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * Helper Method: Get the display time for check-in
     * This handles the logic if the visitor hasn't checked in yet.
     */
    public function getCheckInTimeAttribute()
    {
        return $this->checked_in_at ? $this->checked_in_at->format('H:i') : '---';
    }
}