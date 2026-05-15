<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id('customer_id');
            $table->foreignId('user_id')->unique()->constrained('users', 'user_id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone_number', 20)->unique();
            $table->date('date_of_birth');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
