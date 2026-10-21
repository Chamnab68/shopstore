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
    Schema::create('loyalty_activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->string('action'); // points_earned, points_burned, tier_upgraded
        $table->integer('points_changed');
        $table->integer('points_balance_before');
        $table->integer('points_balance_after');
        $table->string('metadata')->nullable(); // JSON formatted or string (e.g. Order ID)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_activity_logs');
    }
};
