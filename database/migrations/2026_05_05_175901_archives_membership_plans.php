<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamp('archived_at')->nullable();
            $table->unsignedBigInteger('archived_by')->nullable();
            $table->string('archive_reason', 255)->nullable();
            $table->date('last_active_date')->nullable();
            
            $table->foreign('archived_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
            $table->dropColumn(['deleted_at', 'archived_at', 'archived_by', 'archive_reason', 'last_active_date']);
        });
    }
};
