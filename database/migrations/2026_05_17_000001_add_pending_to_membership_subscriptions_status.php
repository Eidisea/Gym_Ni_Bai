<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'pending' to the membership_subscriptions status enum.
     * The controller allows 'pending' in validation, but the original
     * enum only had ['active', 'expired', 'cancelled'].
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE membership_subscriptions MODIFY COLUMN status ENUM('active','expired','cancelled','pending') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        // Remove pending — first ensure no rows use it to avoid DB error
        DB::statement("UPDATE membership_subscriptions SET status = 'cancelled' WHERE status = 'pending'");
        DB::statement("ALTER TABLE membership_subscriptions MODIFY COLUMN status ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active'");
    }
};
