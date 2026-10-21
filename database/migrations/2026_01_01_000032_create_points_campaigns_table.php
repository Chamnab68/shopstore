<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);                    // e.g. "Black Friday 3x Points"
            $table->string('description', 255)->nullable();
            $table->decimal('multiplier', 3, 1)->default(2.0);
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('tier_id')->nullable()->constrained('tiers')->nullOnDelete(); // tier-restricted campaign
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_campaigns');
    }
};
