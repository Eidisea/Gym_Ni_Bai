<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainerProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'trainer_id';

    protected $fillable = [
        // REMOVED user_id
        'first_name',
        'last_name',
        'specialization',
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

    // REMOVED public function user() { ... }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'trainer_id', 'trainer_id');
    }
}
