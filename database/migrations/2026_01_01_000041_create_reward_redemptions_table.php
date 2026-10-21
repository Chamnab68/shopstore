<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reward_id')->constrained()->cascadeOnDelete();
            $table->integer('points_spent');
            $table->string('redemption_code', 30)->unique(); // voucher code to redeem in-store/checkout
            $table->enum('status', ['pending', 'fulfilled', 'expired', 'cancelled'])->default('pending');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete(); // if applied to an order
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_redemptions');
    }
};
