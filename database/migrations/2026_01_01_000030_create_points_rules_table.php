<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);                 // e.g. "Standard Earn Rate", "Weekend Bonus"
            $table->enum('type', ['earn_rate', 'weekend_bonus', 'category_bonus', 'min_order'])->default('earn_rate');
            $table->decimal('points_per_dollar', 5, 2)->default(1.00); // 1 point per $1
            $table->decimal('multiplier', 3, 1)->default(1.0);          // 2x on weekends
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->decimal('min_order_amount', 10, 2)->nullable();      // min order to earn/redeem
            $table->json('active_days')->nullable();      // ["saturday","sunday"] for weekend bonus
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);       // higher = applied first
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_rules');
    }
};
