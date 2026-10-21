<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);                 // Silver, Gold, Platinum
            $table->integer('level')->unique();          // 1, 2, 3 (for ordering/comparison)
            $table->integer('min_spend')->default(0);    // qualification spend threshold
            $table->integer('min_points')->default(0);   // qualification point threshold
            $table->decimal('point_earn_multiplier', 3, 1)->default(1.0); // e.g. Gold = 1.5x points
            $table->boolean('free_shipping')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('exclusive_access')->default(false); // reward-only products
            $table->string('badge_color', 20)->default('#cccccc');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiers');
    }
};
