<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These MUST match the fields you are saving in your Controller.
     */
    protected $fillable = [
        'full_name',
        'phone',
        'type',           
        'guardian_name',  
        'id_number',      // Added: Essential for security
        'host_name',
        'purpose',
        'vehicle_reg',    // Added: For the new vehicle field we added to DB
        'signature',      // Added: To store the Base64 signature string
        'status',         
        'check_in',       
        'checked_out_at'  
    ];

    /**
     * Ensure these fields are treated as date/time objects.
     * This allows you to use ->format('d M, Y') in your Blade files.
     */
    protected $casts = [
        'check_in'       => 'datetime', // Added: Needed for the Digital Badge time display
        'checked_out_at' => 'datetime',
    ];
}