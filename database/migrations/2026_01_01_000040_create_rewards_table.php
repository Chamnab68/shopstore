<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('type', ['voucher', 'free_product', 'experience', 'discount'])->default('voucher');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete(); // for free_product type
            $table->integer('point_cost');
            $table->foreignId('min_tier_id')->nullable()->constrained('tiers')->nullOnDelete(); // tier-exclusive rewards
            $table->integer('total_stock')->nullable();        // null = unlimited
            $table->integer('redeemed_count')->default(0);
            $table->integer('per_member_limit')->nullable();   // max redemptions per customer
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
