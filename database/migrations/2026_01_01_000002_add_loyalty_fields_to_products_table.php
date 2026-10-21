<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_bonus_point')->default(false)->after('stock');
            $table->decimal('point_multiplier', 3, 1)->default(1.0)->after('is_bonus_point');
            $table->boolean('is_reward_only')->default(false)->after('point_multiplier');
            $table->integer('point_cost')->nullable()->after('is_reward_only');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_bonus_point', 'point_multiplier', 'is_reward_only', 'point_cost']);
        });
    }
};
