<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role_id',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // =========================================================================
    // Role Helper Methods
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->role->role_name === Role::ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role->role_name === Role::STAFF;
    }

    public function isCustomer(): bool
    {
        return $this->role->role_name === Role::CUSTOMER;
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role->role_name === $roleName;
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class, 'user_id', 'user_id');
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(StaffProfile::class, 'user_id', 'user_id');
    }

    // =========================================================================
    // Accessor: Resolves a display name from whichever profile exists
    // =========================================================================

    public function getNameAttribute(): string
    {
        $profile = $this->customerProfile 
                ?? $this->staffProfile;

        return $profile 
            ? "{$profile->first_name} {$profile->last_name}" 
            : $this->email;
    }
}
