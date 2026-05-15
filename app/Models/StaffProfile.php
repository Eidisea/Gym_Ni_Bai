<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'staff_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'department',
        'archived_at',
        'archived_by',
        'archive_reason',
        'last_active_date',
    ];

    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
            'last_active_date' => 'date',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function cashPayments(): HasMany
    {
        return $this->hasMany(CashPayment::class, 'staff_id', 'staff_id');
    }
}
