<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();   // e.g. "rewards.create", "tiers.override"
            $table->string('module', 50);             // e.g. "rewards", "tiers", "points"
            $table->string('description', 255)->nullable();
            $table->string('min_tier', 20)->nullable(); // e.g. "platinum" for tier-gated features
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
