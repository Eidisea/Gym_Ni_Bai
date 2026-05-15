<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ewallet_payments', function (Blueprint $table) {
            $table->id('ewallet_payment_id');
            $table->foreignId('transaction_id')->unique()->constrained('payment_transactions', 'transaction_id')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('provider', ['gcash', 'paymaya', 'grabpay', 'paypal']);
            $table->string('reference_number', 100);
            $table->string('account_identifier', 100)->nullable(); // Phone number or email
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ewallet_payments');
    }
};
