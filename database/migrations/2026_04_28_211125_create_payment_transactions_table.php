<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->foreignId('customer_id')->constrained('customer_profiles', 'customer_id')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('membership_subscriptions', 'subscription_id')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('class_bookings', 'booking_id')->onDelete('restrict')->onUpdate('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'ewallet']);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('transaction_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
