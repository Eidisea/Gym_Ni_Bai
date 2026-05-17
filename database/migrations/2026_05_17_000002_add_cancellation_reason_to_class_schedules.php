<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add cancellation_reason column to class_schedules.
     * ClassScheduleController::cancel() was writing to a non-existent 'location'
     * column — this adds the proper column for it.
     */
    public function up(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->string('cancellation_reason', 500)->nullable()->after('archive_reason');
        });
    }

    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
        });
    }
};
