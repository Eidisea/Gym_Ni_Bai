<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id('schedule_id');
            $table->foreignId('class_id')->constrained('fitness_classes', 'class_id')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('trainer_id')->constrained('trainer_profiles', 'trainer_id')->onDelete('restrict')->onUpdate('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('available_slots');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
