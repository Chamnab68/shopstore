<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->integer('referrer_reward_points');     // points the referrer earns
            $table->integer('referee_reward_points');       // points the new member earns
            $table->decimal('commission_percent', 5, 2)->default(0); // for affiliate-style % commission
            $table->integer('max_levels')->default(1);      // multi-level: 1 = direct only, 2 = + sub-referrals
            $table->decimal('level2_commission_percent', 5, 2)->default(0);
            $table->boolean('requires_first_purchase')->default(true); // conversion only counts after first order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_programs');
    }
};
