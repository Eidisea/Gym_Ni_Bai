<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'attended' and 'no_show' to the class_bookings status enum.
     * Controllers filter and check for these statuses but the original
     * enum only had ['confirmed', 'cancelled', 'completed'].
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE class_bookings MODIFY COLUMN status ENUM('confirmed','cancelled','completed','attended','no_show') NOT NULL DEFAULT 'confirmed'");
    }

    public function down(): void
    {
        // Revert non-standard statuses before shrinking enum
        DB::statement("UPDATE class_bookings SET status = 'completed' WHERE status IN ('attended', 'no_show')");
        DB::statement("ALTER TABLE class_bookings MODIFY COLUMN status ENUM('confirmed','cancelled','completed') NOT NULL DEFAULT 'confirmed'");
    }
};
