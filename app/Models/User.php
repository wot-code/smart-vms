<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Role Constants for consistent usage across the app
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_HOST  = 'host';
    const ROLE_GUARD = 'guard';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone', // Supports SMS alerts and visitor notifications
        'role',  // Defines system permissions (admin, host, guard)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Role Helper Methods
     * --------------------------------------------------------------------------
     */

    /**
     * Check if the user is an Administrator
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if the user is a Host/Resident
     */
    public function isHost(): bool
    {
        return $this->role === self::ROLE_HOST;
    }

    /**
     * Check if the user is a Security Guard
     */
    public function isGuard(): bool
    {
        return $this->role === self::ROLE_GUARD;
    }

    /**
     * Get the formatted display name for the role
     */
    public function getRoleLabelAttribute(): string
    {
        return ucfirst($this->role ?? 'User');
    }
}