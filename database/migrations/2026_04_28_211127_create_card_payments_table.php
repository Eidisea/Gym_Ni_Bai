<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_payments', function (Blueprint $table) {
            $table->id('card_payment_id');
            $table->foreignId('transaction_id')->unique()->constrained('payment_transactions', 'transaction_id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('card_last_four', 4);
            $table->enum('card_type', ['visa', 'mastercard', 'amex', 'discover']);
            $table->string('authorization_code', 50);
            $table->string('processor_reference', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_payments');
    }
};
