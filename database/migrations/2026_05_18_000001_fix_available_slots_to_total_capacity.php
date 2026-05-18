<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix available_slots to be the total capacity (max_participants from fitness_classes).
     *
     * Previously, available_slots was being DECREMENTED on each booking, meaning it stored
     * the REMAINING slots rather than the total capacity. This caused the "class is full"
     * bug where the booking check saw 0 remaining even though the class showed slots available.
     *
     * Now, available_slots = total capacity (immutable), and remaining slots are computed
     * dynamically as: available_slots - COUNT(confirmed bookings).
     *
     * This migration restores all class_schedule.available_slots to the correct total capacity
     * from the linked fitness_class.max_participants.
     */
    public function up(): void
    {
        DB::statement('
            UPDATE class_schedules cs
            JOIN fitness_classes fc ON cs.class_id = fc.class_id
            SET cs.available_slots = fc.max_participants
            WHERE cs.deleted_at IS NULL
        ');
    }

    public function down(): void
    {
        // Cannot safely reverse this — the original decremented values are lost.
        // Reversing would require knowing how many confirmed bookings existed at migration time.
    }
};
