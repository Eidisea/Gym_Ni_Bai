<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('customer_id')->constrained('customer_profiles', 'customer_id')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('schedule_id')->constrained('class_schedules', 'schedule_id')->onDelete('restrict')->onUpdate('cascade');
            $table->enum('status', ['confirmed', 'cancelled', 'completed'])->default('confirmed');
            $table->timestamp('booked_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_bookings');
    }
};
