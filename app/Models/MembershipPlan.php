<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipPlan extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'plan_id';

    protected $fillable = [
        'plan_name',
        'duration_days',
        'base_price',
        'archived_at',
        'archived_by',
        'archive_reason',
        'last_active_date',
    ];

    protected function casts(): array
    {
        return [
            'base_price'    => 'decimal:2',
            'duration_days' => 'integer',
            'archived_at' => 'datetime',
            'last_active_date' => 'date',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(MembershipSubscription::class, 'plan_id', 'plan_id');
    }
}
