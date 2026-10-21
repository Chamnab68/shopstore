<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tier_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tier_id')->constrained()->cascadeOnDelete();
            $table->string('benefit_key', 50);     // e.g. "birthday_bonus", "early_access"
            $table->string('benefit_value', 255);  // e.g. "500 points" or "true"
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_benefits');
    }
};
