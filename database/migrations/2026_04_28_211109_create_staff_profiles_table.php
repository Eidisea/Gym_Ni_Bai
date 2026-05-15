<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id('staff_id');
            $table->foreignId('user_id')->unique()->constrained('users', 'user_id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('department', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
    }
};
