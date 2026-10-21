<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['earn', 'burn', 'expire', 'manual_adjust', 'referral_bonus', 'campaign_bonus'])->default('earn');
            $table->integer('points');                     // positive for earn, negative for burn/expire
            $table->integer('balance_after');               // snapshot balance after this txn
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('points_rule_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reward_redemption_id')->nullable()->constrained('reward_redemptions')->nullOnDelete();
            $table->string('reason', 255)->nullable();      // required for manual_adjust
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete(); // admin who manually adjusted
            $table->date('expires_at')->nullable();         // expiry date for this batch of points
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};
