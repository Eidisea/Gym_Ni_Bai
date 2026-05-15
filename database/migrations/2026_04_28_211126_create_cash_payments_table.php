<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_payments', function (Blueprint $table) {
            $table->id('cash_payment_id');
            $table->foreignId('transaction_id')->unique()->constrained('payment_transactions', 'transaction_id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('staff_id')->constrained('staff_profiles', 'staff_id')->onDelete('restrict')->onUpdate('cascade');
            $table->decimal('amount_received', 10, 2);
            $table->decimal('change_given', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_payments');
    }
};
