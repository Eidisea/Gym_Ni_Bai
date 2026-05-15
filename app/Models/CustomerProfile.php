<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'date_of_birth',
        'archived_at',
        'archived_by',
        'archive_reason',
        'last_active_date',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'archived_at' => 'datetime',
            'last_active_date' => 'date',
        ];
    }

    // Accessor: full name convenience
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(MembershipSubscription::class, 'customer_id', 'customer_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ClassBooking::class, 'customer_id', 'customer_id');
    }
}
