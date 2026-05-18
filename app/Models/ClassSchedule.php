<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'schedule_id';

    public function getRouteKeyName()
    {
        return 'schedule_id';
    }

    protected $fillable = [
        'class_id',
        'trainer_id',
        'start_time',
        'end_time',
        'available_slots',
        'archived_at',
        'archived_by',
        'archive_reason',
        'cancellation_reason',
        'last_active_date',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'available_slots' => 'integer',
            'archived_at' => 'datetime',
            'last_active_date' => 'date',
        ];
    }

    // Relationships
    public function fitnessClass(): BelongsTo
    {
        return $this->belongsTo(FitnessClass::class, 'class_id', 'class_id');
    }

    public function trainerProfile(): BelongsTo
    {
        return $this->belongsTo(TrainerProfile::class, 'trainer_id', 'trainer_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ClassBooking::class, 'schedule_id', 'schedule_id');
    }

    // Helper methods
    /**
     * Number of confirmed bookings for this schedule.
     */
    public function getBookedSlotsAttribute(): int
    {
        return $this->bookings()->where('status', 'confirmed')->count();
    }

    /**
     * Remaining bookable slots.
     * available_slots is the TOTAL capacity (immutable).
     * Remaining = total capacity minus confirmed bookings.
     */
    public function getRemainingSlotsAttribute(): int
    {
        return max(0, $this->available_slots - $this->booked_slots);
    }

    public function isFullyBooked(): bool
    {
        return $this->remaining_slots <= 0;
    }
}