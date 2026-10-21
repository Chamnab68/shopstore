<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->enum('trigger_type', [
                'first_purchase', 'order_milestone', 'referral_milestone',
                'login_streak', 'points_milestone', 'manual',
            ])->default('first_purchase');
            $table->integer('trigger_value')->nullable(); // e.g. 10 for "10-order milestone"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
