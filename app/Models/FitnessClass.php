<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FitnessClass extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'class_id';

    protected $fillable = [
        'class_name',
        'description',
        'max_participants',
        'archived_at',
        'archived_by',
        'archive_reason',
        'last_active_date',
    ];

    protected function casts(): array
    {
        return [
            'max_participants' => 'integer',
            'archived_at' => 'datetime',
            'last_active_date' => 'date',
        ];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'class_id', 'class_id');
    }
}
