<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name',
    ];

    // Constants for use throughout the app — avoids magic strings
    const ADMIN = 'Admin';
    const STAFF = 'Staff';
    const CUSTOMER = 'Customer';

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}