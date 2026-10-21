<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id');
            $table->foreignId('tier_id')->nullable()->after('role_id');
            $table->integer('points_balance')->default(0)->after('tier_id');
            $table->integer('lifetime_points')->default(0)->after('points_balance');
            $table->string('referral_code', 20)->unique()->nullable()->after('lifetime_points');
            $table->foreignId('referred_by')->nullable()->after('referral_code');
            $table->timestamp('tier_since')->nullable()->after('referred_by');
            $table->timestamp('login_streak_updated_at')->nullable()->after('tier_since');
            $table->integer('login_streak_count')->default(0)->after('login_streak_updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role_id', 'tier_id', 'points_balance', 'lifetime_points',
                'referral_code', 'referred_by', 'tier_since',
                'login_streak_updated_at', 'login_streak_count',
            ]);
        });
    }
};
