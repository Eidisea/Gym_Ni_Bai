<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_subscriptions', function (Blueprint $table) {
            $table->id('subscription_id');
            $table->foreignId('customer_id')->constrained('customer_profiles', 'customer_id')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('plan_id')->constrained('membership_plans', 'plan_id')->onDelete('restrict')->onUpdate('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_subscriptions');
    }
};
